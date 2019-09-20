(function($) {

var name = 'LodgixResponsiveTable';
var count = 0;

$.widget("ui.LodgixResponsiveTable", {

	_create: function() {
		this._element = this.element.clone();
        var wrapper = $('<div style="text-align:center"></div>');
        wrapper.attr('class', this.element.attr('class'));
        wrapper.data(this.element.data());
        this.element.before(wrapper);
        this.element.detach();
        this.element = wrapper;
		this._parse();
	},

	destroy: function() {
        $.Widget.prototype.destroy.call(this);
        this.element.before(this._element);
        this.element.detach();
        this.element = this._element;
        this._element = null;
	},

	_init: function() {
		var w = this;
		w._id = w.element.attr('id');
		if (!w._id) {
			w._id = name + count++;
			w.element.attr('id', w._id);
		}
		w.element.addClass(w._element[0].className);
		w._render();
		$(window).resize(function() {
			w._render();
		});
	},

	_parse: function() {
		var rows = this._rows = [];
		this._totalWidth = 0;
		var totalAltWidth = 0;
		var cellWidth = 0;
		var trs = $('tr', this._element).filter(function() {
			return $(this).css('display') !== 'none';
		});
		var len = trs.length;
		var r, row, tr, td;
		for (r = 0; r < len; r++) {
			row = {};
			tr = trs[r];
			row.trClassTitle =  $(tr).attr('class');
			td = $('th', tr);
			row.title = td.html();
			row.width = td.attr('width') * 1;
			if (row.width) {
				this._totalWidth += row.width;
				if (row.width > cellWidth) {
					cellWidth = row.width;
				}
			}
			row.alt = td.attr('alt');
			if (!row.alt) {
				row.alt = row.title;
			}
			row.altWidth = td.attr('altwidth') * 1;
			if (!row.altWidth) {
				row.altWidth = row.width;
			}
			if (row.altWidth) {
				totalAltWidth += row.altWidth;
			}
			row.classTitle = td.attr('class');			
			td = $('td', tr);
			row.value = td.html();
			row.classValue = td.attr('class');
			rows.push(row);
		}
		this._requiredWidth = cellWidth * len;
		if (this._totalWidth) {
			for (r = 0; r < len; r++) {
				row = rows[r];
				if (row.width) {
					row.widthPercent = 100 * row.width / this._totalWidth;
				}
			}
		}
		if (totalAltWidth) {
			for (r = 0; r < len; r++) {
				row = rows[r];
				if (row.altWidth) {
					row.altWidthPercent = 100 * row.altWidth / totalAltWidth;
				}
			}
		}
		this._cellWidthPercent = 100 / len;
	},

	_render: function() {
		var html = [];
		var rows = this._rows;
		var len = rows.length;
		var containerWidth = this.element.innerWidth();
		var layout = 0;
		if (containerWidth < this._requiredWidth) {
			layout = containerWidth >= this._totalWidth ? 1 : 2;
		}
		var r, row;
		for (r = 0; r < len; r++) {
			row = rows[r];
			html.push('<div style="display:inline-block;');
			if (layout == 0) {
				html.push('min-width:');
				html.push(this._cellWidthPercent);
				html.push('%;');
			} else if (layout == 1) {
				if (row.widthPercent) {
					html.push('min-width:');
					html.push(row.widthPercent);
					html.push('%;');
				}
			} else {
				if (row.altWidthPercent) {
					html.push('min-width:');
					html.push(row.altWidthPercent);
					html.push('%;');
				}
			}
			html.push('vertical-align:top;" ');
			if (row.trClassTitle) {
				html.push(' class="');
				html.push(row.trClassTitle);
				html.push('"');
			}			
			html.push('><div');
			if (row.classTitle) {
				html.push(' class="');
				html.push(row.classTitle);
				html.push('"');
			}
			html.push('>');
			if (layout == 2) {
				html.push(row.alt);
			} else {
				html.push(row.title);
			}
			html.push('</div><div');
			if (row.classValue) {
				html.push(' class="');
				html.push(row.classValue);
				html.push('"');
			}
			html.push('>');
			html.push(row.value);
			html.push('</div></div>');
		}
		this.element.html(html.join(''));
	}

});

})(jQueryLodgix);
