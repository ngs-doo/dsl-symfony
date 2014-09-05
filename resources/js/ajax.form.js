if (typeof NGS == "undefined") NGS = { };

NGS.AjaxFormOptions = function (options) {
	this.hide_search = true;
	this.confirm_delete = "Are you sure?";
	this.cancel_add = "Discard changes";
	this.save_change = "Save changes";
	this.save_add = "Add new item";

	if (!options)
		return this;

	if (typeof options.hide_search != "undefined")
		this.hide_search = options.hide_search;

	if (typeof options.confirm_delete != "undefined")
		this.confirm_delete = options.confirm_delete;

	if (typeof options.cancel_add != "undefined")
		this.cancel_add = options.cancel_add;

	if (typeof options.save_change != "undefined")
		this.save_change = options.save_change;

	if (typeof options.save_add != "undefined")
		this.save_add = options.save_add;

	return this;
}

NGS.AjaxFormBase = function (URI, tableUrl, options) {
	this.URI = URI;
	this.formAction = "add";

	this.formContainer = $('<div></div>');

	this._tableUrl = tableUrl;
	this.tableParameters = null;
	this.tableContainer = $('<div></div>');

	this.options = new NGS.AjaxFormOptions (options);

	return this;
};

NGS.AjaxFormBase.prototype.tableUrl = function () {
	if (this.tableParameters)
		return this._tableUrl + '?' + this.tableParameters;

	return this._tableUrl + '?_pagination=off';
}

NGS.AjaxFormBase.prototype.loadTable = function (data) {
	var self = this;

	self.tableContainer.html (data);
	var form = self.tableContainer.find('form').eq(0);

	// add new article button
	self.tableContainer.find('a.action-add').hide();

	// clear search button
	self.tableContainer.find('a.clear-search').click (function (event) {
		event.preventDefault();
		self.tableParameters = null;
		self.refreshTable();
	});

	// article search form
	self.tableContainer.find('form.search').submit (function (event) {
		event.preventDefault();
		self.tableParameters = form.serialize();
		self.refreshTable();
	});

	// edit buttons
	$("td > a.btn").each (function () {
		$(this).click (function (event) {
			event.preventDefault();

			self.editUrl = $(this).attr ("href");
			self.formAction = "edit";
			self.loadForm();

		});
	});

	// delete buttons
	$("td > form").each (function () {
		$(this).off('submit').submit (function (event)  {
			event.preventDefault();

			if (confirm ('Are you sure?'))
				$.post ($(this).attr ("action"), $(this).serialize(), function (html) {
					self.refreshTable(html);
				});
		});
	});
}

// table of inserted elements
NGS.AjaxFormBase.prototype.refreshTable = function (cache) {
	var self = this;

	if (cache)
		this.loadTable (cache);
	else
		$.get (this.tableUrl(), this.tableParameters, function (data) {
			self.loadTable (data);
		});
};

NGS.AjaxFormBase.prototype.loadForm = function () {
	var self = this;

	var form = self.formContainer.find ('form').eq(0);
	form.submit (function (event) {
		event.preventDefault();

		$.post (form.attr ("action"), form.serialize(), function (data) {
			self.refreshTable();
			if (self.formAction == "edit") {
				self.formAction = "add";
			} 

			self.loadForm();

		}, 'json');
	});

	var cancelButton = form.find ('a.cancel').eq(0);
	cancelButton.text (this.options.cancel_add);
	cancelButton.click (function (event) {
		event.preventDefault();

		self.formAction = "add";
		self.loadForm();
	});

	var submitButton = form.find ("button[type=submit]");
	if (self.formAction == "edit")
		submitButton.text (this.options.save_change);
	else
		submitButton.text (this.options.save_add);


	form.get(0).scrollIntoView();
};

NGS.AjaxFormBase.prototype.attachTable = function (e) {
	$(e).append (this.tableContainer);
};

NGS.AjaxFormBase.prototype.show = function () {
	this.refreshTable();
}




NGS.AjaxForm = function (URI, addUrl, tableUrl, options) {
	if (arguments.length == 1 && typeof URI == "object")
		return new NGS.AjaxForm (URI.URI, URI.addUrl, URI.tableUrl, URI.options);

	NGS.AjaxFormBase.call (this, URI, tableUrl, options);
	this.formCache = null;
	this.editUrl = null;
	this.addUrl = addUrl;
};

NGS.AjaxForm.prototype = new NGS.AjaxFormBase();

NGS.AjaxForm.prototype.formUrl = function () {
	if (this.formAction == "add")
		return this.addUrl + this.URI;

	return this.editUrl;
};

NGS.AjaxForm.prototype.loadForm = function () {
	var self = this;

	if (self.formCache && self.formAction != 'edit') {
		self.formContainer.html (self.formCache);
		NGS.AjaxFormBase.prototype.loadForm.call (self);
	} else
		$.get (this.formUrl(), function (html) {
			if (self.formAction != 'edit')
				self.formCache = html;

			self.formContainer.html (html);
			NGS.AjaxFormBase.prototype.loadForm.call (self);
		}, 'html');
}

NGS.AjaxForm.prototype.attachForm = function (e) {
	$(e).append (this.formContainer);
}

NGS.AjaxForm.prototype.show = function () {
	this.loadForm();
	NGS.AjaxFormBase.prototype.show.call (this);
}



NGS.AjaxFormStatic = function (formEntity, options) {
	this.formEntity = formEntity;
}
