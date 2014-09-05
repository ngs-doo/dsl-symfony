<?php
namespace NGS\Symfony\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Form;
use NGS\Patterns\CubeBuilder;

abstract class OlapController extends Controller
{
    abstract protected function getCube();

    abstract protected function getFilter();

    protected function getFacts()
    {
        return $this->getCube()->getFacts();
    }

    protected function getDimensions()
    {
        return $this->getCube()->getDimensions();
    }

    protected function getDefaultDimensions()
    {
        return $this->getDimensions();
    }

    protected function getDefaultFacts()
    {
        return $this->getFacts();
    }

    protected function findTemplates()
    {
        $templateName = 'php:cube:'.\NGS\Name::full($this->getCube());
        return \Settings\Template::FindByName($templateName);
    }

    protected function hasTemplating()
    {
        return class_exists('\Settings\Template');
    }

    protected function getCubeData(Request $request)
    {
        $cubeParams = array();
        if ($templateUri = $request->request->get('_template')) {
            $template = \Settings\Template::find($templateUri);
            $cubeParams = json_decode($template->Content->value, true);
        }
        elseif ($request->getMethod()==='POST') {
            $cubeParams = $request->request->all();
        }

        if (isset($cubeParams['_dimensions'])) {
            $chosenDimensions = array_keys($cubeParams['_dimensions']);
        } elseif($request->getMethod()==='GET') {
            $chosenDimensions = $this->getDefaultDimensions();
        } else {
            $chosenDimensions = array();
        }

        if (isset($cubeParams['_facts'])) {
            $chosenFacts = array_keys($cubeParams['_facts']);
        } elseif($request->getMethod()==='GET') {
            $chosenFacts = $this->getDefaultFacts();
        } else {
            $chosenFacts = array();
        }

        // check if template is out of date with invalid facts/dimensions
        if ($templateUri && (
            count($chosenDimensions) !== count(array_intersect($chosenDimensions, $this->getDimensions()))
            || count($chosenFacts) !== count(array_intersect($chosenFacts, $this->getFacts())))) {
                throw new \LogicException('Template "'.$template->Title.'"" is invalid or out of date');
        }

        $filter = $this->getFilter();
        if(!$filter instanceof Form)
            $filter = $this->createForm($filter);
        if($request->getMethod()==='POST')
            $filter->bind($request);
        $specification = $filter->getData();

        $cube = $this->getCube();
        $builder = new CubeBuilder($cube);
        $builder
            ->dimensions($chosenDimensions)
            ->facts($chosenFacts)
            ->with($specification);

        if($order = $request->get('_order')) {
            if(is_string($order))
                $order = array($order);
            $order = array_intersect($order, array_merge($chosenFacts, $chosenDimensions));
            foreach($order as $property)
                $builder->asc($property);
        }

        $items = $builder->analyze();

        $result = array(
            'items'             => $items,
            'dimensions'        => $this->getDimensions(),
            'facts'             => $this->getFacts(),
            'filter'            => $filter->createView(),
            'chosen_dimensions' => $chosenDimensions,
            'chosen_facts'      => $chosenFacts,
            'order'             => $order,
        );
        if ($this->hasTemplating()) {
            $result['template'] = array(
                'items'  => $this->findTemplates(),
                'chosen' => $templateUri,
                'name'   => 'php:cube:'.\NGS\Name::full($cube),
            );
        }
        return $result;
    }

    protected function getReportHeaders($cube)
    {
        $columns = array_merge($cube['chosen_dimensions'], $cube['chosen_facts']);
        $headers = array();
        foreach($columns as $col)
            $headers[] = $this->get('translator')->trans($col);
        return $headers;
    }

    protected function getReportRows($cube)
    {
        $rows = array();
        foreach($cube['items'] as $item) {
            $row = array();
            foreach($item as $prop=>$val)
                $row[] = $val;
            $rows[] = $row;
        }
        return $rows;
    }

    protected function downloadReport($content, $mimeType, $filename)
    {
        return new Response($content, 200, array(
            'Content-Type'        => $mimeType,
            'Content-disposition' => 'attachment'.($filename ? ';filename='.$filename : '')
        ));
    }

    /**
     * @Route("/")
     * @Method({"GET", "POST"})
     * @Template("ModelBundle:Olap:cube.html.twig")
     */
    public function indexAction(Request $request)
    {
        return $this->getCubeData($request);
    }

    /**
     * @Route("/csv")
     * @Method({"POST"})
     */
    public function csvReportAction(Request $request)
    {
        $cube    = $this->getCubeData($request);
        $headers = $this->getReportHeaders($cube);
        $rows    = $this->getReportRows($cube);

        $fieldSeparator = ";";
        $lineSeparator  = "\n";

        $csvRows = array(implode($fieldSeparator, $headers));
        foreach($rows as $row) {
            $csvRow = array();
            foreach($row as $cell)
                $csvRow[] = '"'.str_replace('"', '""', $cell).'"';
            $csvRows[] = implode($fieldSeparator, $csvRow);
        }
        $content = implode($lineSeparator, $csvRows);

        return $this->downloadReport($content, 'application/vnd.ms-excel', 'report.csv');
    }

    /**
     * @Route("/xlsx")
     * @Method({"POST"})
     */
    public function xlsReportAction(Request $request)
    {
        $cube    = $this->getCubeData($request);
        $headers = $this->getReportHeaders($cube);
        $rows    = $this->getReportRows($cube);

        foreach($headers as &$title)
            $title = str_replace(' ', "\n", $this->get('translator')->trans($title));
        $rows = array_merge(array($headers), $rows);

        $excel = new \PHPExcel();
        $sheet = $excel->getActiveSheet();
        // 2nd param is default value for empty cells
        $sheet->fromArray($rows, null, 'A1');
        // autoresize width for each column
        $lastColumn = $sheet->getHighestColumn();
        $lastColumn++;
        for ($column = 'A'; $column != $lastColumn; $column++)
            $sheet->getColumnDimension($column)->setAutoSize(true);

        $writer = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        ob_start();
        $writer->save('php://output');
        $output = ob_get_clean();

        $mime = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        return $this->downloadReport($output, $mime, 'report.xlsx');
    }

    /**
     * @Route("/pdf")
     * @Method({"POST"})
     */
    public function pdfReportAction(Request $request)
    {
        $cube    = $this->getCubeData($request);
        $headers = $this->getReportHeaders($cube);
        $rows    = $this->getReportRows($cube);
        $rows = array_merge(array($headers), $rows);

        $excel = new \PHPExcel();
        $sheet = $excel->getActiveSheet();
        $sheet->fromArray($rows, null, 'A1');
        $sheet->calculateColumnWidths();

        $writer = \PHPExcel_IOFactory::createWriter($excel, 'PDF');
        ob_start();
        $writer->save('php://output');
        $output = ob_get_clean();

        return $this->downloadReport($output, 'application/pdf', 'report.xlsx');
    }
}
