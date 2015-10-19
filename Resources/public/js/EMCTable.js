
/**
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */

function EMCTable(dom) {
    if (EMCTable.caller !== EMCTable.handle) {
        throw new Error("This object cannot be instanciated");
    }

    this.$dom = $(dom);
    this.route = this.$dom.data('route');
    this.subtableRoute = this.$dom.data('subtableRoute');
    this.exportRoute = this.$dom.data('exportRoute');
    this.selectRoute = this.$dom.data('selectRoute');
    this.limit = this.$dom.data('limit');
    this.selectable = this.$dom.data('selectable');
    this.selectedRows = {};

    this.init();
}

EMCTable.DEFAULT_LIMIT = 10;
EMCTable.EVENT_INIT = 'emc.table.init';
EMCTable.EVENT_SELECT = 'emc.table.select';
EMCTable.EVENT_FIND = 'emc.table.find';
EMCTable.EVENT_CHANGE = 'emc.table.change';
EMCTable.EVENT_SUBTABLE = 'emc.table.subtable';

EMCTable.prototype.constructor = EMCTable;

EMCTable.instances = {};

EMCTable.getInstance = function(table) {
    if (!table instanceof Element || table === null || table.nodeName !== "TABLE") {
        throw new Error('EMCTable.getInstance : Element TABLE node required');
    }

    if (!table.id in this.instances) {
        throw new Error('table #' + table.id + ' not handled');
    }

    return this.instances[table.id];
};

EMCTable.handle = function(dom) {
    if (!(dom instanceof HTMLElement)) {
        throw new Error('EMCTable.handle : dom HTMLElement is required');
    }
    this.instances[dom.id] = new EMCTable(dom);
};

EMCTable.prototype.init = function() {
    var that = this;

    this.timer = null;
    this.sort = 0;

    this.$filter = this.$dom.find('thead > tr > td > div.right > .filter > input[name=_filter]');
    this.$filter.on('keyup', function(event) {
        clearTimeout(that.timer);
        var filter = $(this).val().length;
        if (filter > 0 && filter < 3) {
            return;
        }
        that.timer = setTimeout(function() {
            that.find(1, that.sort);
        }, 500);
    });
    this.$filter.val('');

    if (this.exportRoute) {
        this.$dom.find('thead > tr > td > div.right > .export > ul > li > a[data-export]')
                .on('click', function(event) {
                    var type = $(this).data('export');
                    that.export(type);
                });
    }

    this.$dom.on('click', '> thead > tr > th[data-sort]', function(event) {
        $(this.parentNode).find('> th.sort').removeClass('sort');

        $(this).addClass('sort')
                .toggleClass('dropup');

        var sort = parseInt($(this).data('sort'));
        that.sort = $(this).hasClass('dropup') ? -sort : sort;
        that.find(1);
    });

    this.$dom.on('change', '> tfoot > tr > td > select', function(event) {
        that.limit = $(this).val();
        that.find(1);
    });

    this.$dom.on('click', '> tfoot > tr > td > ul.pagination > li > a', function(event) {
        that.find($(this).data('page'));
    });

    if (this.selectRoute) {

        this.selectAll(false);

        this.$dom.find('> thead > tr > td > div.left > .selection > button:first').on('click', function(event) {
            var state = this.firstChild.className.indexOf(this.firstChild.getAttribute('icon-checked')) === -1;
            that.selectPage(state);
        });

        this.$dom.find('> thead > tr > td > div.left > .selection > ul > li > a').on('click', function(event) {
            switch ($(this.parentNode).index()) {
                case 0 :
                    that.selectAll(true, true);
                    return;
                case 1 :
                    that.selectAll(false, true);
                    return;
                case 2 :
                    that.selectPage(true);
                    return;
                case 3 :
                    that.selectPage(false);
                    return;
            }
        });

        this.$dom.on('change', '> tbody > tr:not(.empty) > td.column-select-checkbox > input[type=checkbox]', function(event) {
            that.select(this);
        });

        this.$dom.on(EMCTable.EVENT_CHANGE, function(event) {
            that.$dom.find('> tbody > tr').each(function() {
                if (this.id in that.selectedRows) {
                    $(this).addClass('info')
                            .find('> td.column-select-checkbox > input[type=checkbox]')
                            .prop("checked", true);
                }
            });
        });

        var button = this.$dom.find('> thead > tr > td > div.right > .export > button:first').get(0);
        $(button).prop('disabled', true);
        this.$dom.on(EMCTable.EVENT_SELECT, function(event, selectedRows) {
            $(button).attr('disabled', Object.keys(selectedRows).length === 0);
        });
    }

    if (this.subtableRoute !== null) {
        this.$dom.on(EMCTable.EVENT_SUBTABLE, function(event, table) {
            if (typeof ($.fn.selectpicker) === "function") {
                $(table).find('> tfoot > tr > td > select').selectpicker();
            }
        });
    }

    if (this.subtableRoute !== null || this.selectRoute) {
        this.$dom.on('click', '> tbody > tr[data-subtable], > tbody > tr[data-selectable]', function(event) {
            if ((event.target.nodeName === "INPUT" && event.target.parentNode.className === 'column-select-checkbox')
                    || event.target.nodeName === "A"
                    || event.target.nodeName === "BUTTON"
                    ) {
                return;
            }

            if (that.selectRoute && event.ctrlKey) {
                var input = $(this).find('> td.column-select-checkbox > input[type=checkbox]').get(0);
                input.checked = !input.checked;
                that.select(input);
            } else if (that.subtableRoute !== null && !event.ctrlKey) {
                that.openSubtable(this);
            }
            event.preventDefault();
            event.stopPropagation();
        });
    }

    that.$dom.trigger(EMCTable.EVENT_INIT);
};

EMCTable.prototype.find = function(page) {

    this.$dom.trigger(EMCTable.EVENT_FIND);

    var height = this.$dom.height();

    var html = this.request.get(this.route, {query: this.getQuery(page)}, 'HTML');

    var $html = $(html);

    var $pages = $html.find('> ul');
    var $rows = $html.find('> table > tbody');

    var $tbody = this.$dom.find('tbody').empty();
    var $ul = this.$dom.find('tfoot > tr > td > ul').empty();
    var $select = this.$dom.find('tfoot > tr > td > select');
    if ($rows.length > 0) {
        $tbody.append($rows.children());
        $ul.append($pages.children());
        $select.prop('disabled', false);
    } else {
        $tbody.append(
                '<td colspan="' + this.$dom.find('> thead > tr > th').length + '">'
                + '<center>' + EMCTable.EMPTY + '</center>'
                + '</td>'
                );
        $ul.empty();
        $select.prop('disabled', true);
    }

    var $emptyRows = this.$dom.find('> tbody > tr.empty > td');
    if ($emptyRows.length) {
        $emptyRows.find('> div').css('height', (height - this.$dom.height()) / $emptyRows.length);
    }

    delete(html);
    delete($html);

    this.$dom.trigger(EMCTable.EVENT_CHANGE);
};

EMCTable.prototype.openSubtable = function(tr) {

    if (tr.nextSibling.className === "subtable") {
        if (!$(tr.nextSibling).is(':visible')) {
            $(tr.parentNode).find('> tr.subtable').not(tr.nextSibling).hide();
        }
        $(tr.nextSibling).toggle();
        return;
    }

    $(tr.parentNode).find('> tr.subtable').hide();

    var $td = $(document.createElement('td'))
                .attr('colspan', tr.childElementCount)
                .append('<center><i class="fa fa-spinner fa-pulse"></i></center>');

    var $tr = $(tr);
    $tr.after(
        $(document.createElement('tr'))
            .addClass('subtable')
            .append($td)
    );

    var data = $tr.data();

    var html = this.request.get(this.subtableRoute, {params: data.subtable, subtable: true}, 'HTML');

    $td.html(html);

    this.$dom.trigger(EMCTable.EVENT_CHANGE);
    this.$dom.trigger(EMCTable.EVENT_SUBTABLE, [$td.find('> table').get(0)]);
};

EMCTable.prototype.getRows = function() {
    return this.request.get(this.selectRoute, {query: this.getQuery()}, 'JSON');
};

EMCTable.prototype.export = function(type) {

    var append_recursive = function(form, data, parentKey) {
        for (var key in data) {
            var name = parentKey.length > 0 ? parentKey + '[' + key + ']' : key;
            if (typeof (data[key]) === "object") {
                append_recursive(form, data[key], name);
            } else {
                var input = document.createElement('input');
                input.setAttribute('type', 'hidden');
                input.setAttribute('name', name);
                input.setAttribute('value', data[key]);
                form.appendChild(input);
            }
        }
    };

    var form = document.createElement('form');
    form.setAttribute('method', 'POST');
    form.setAttribute('action', this.exportRoute);
    form.setAttribute('target', '_blank');


    var data = {type: type};
    data.query = $.extend(this.getQuery(), {limit: 0});
    data.query.selectedRows = this.selectedRows;

    append_recursive(form, data, '');

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
};

EMCTable.prototype.select = function(input) {
    var tr = input.parentNode.parentNode;
    var data = this.getRowData(tr);

    $(tr).toggleClass('info', input.checked);
    if (!input.checked) {
        delete(this.selectedRows[ tr.id ]);
        this.$dom.trigger(EMCTable.EVENT_SELECT, [this.selectedRows, data, false]);
        return;
    }

    this.selectedRows[ tr.id ] = data;
    this.$dom.trigger(EMCTable.EVENT_SELECT, [this.selectedRows, data, true]);
};

EMCTable.prototype.selectAll = function(state, remote) {

    if (typeof (remote) === "boolean" && remote) {
        var rows = this.getRows();
        this.updateSelectedRows(rows, state);
    }

    var $icon = this.$dom.find('> thead > tr > td > div.left > .selection > button:first > i');
    $icon.toggleClass($icon.attr('icon-checked'), state);
    $icon.toggleClass($icon.attr('icon-unchecked'), !state);

    this.$dom.find('> tbody > tr:not(.empty)').toggleClass('info', state);
    this.$dom.find('> tbody > tr:not(.empty) > td.column-select-checkbox > input[type=checkbox]').prop('checked', state);

    this.$dom.trigger(EMCTable.EVENT_SELECT, [this.selectedRows]);
};

EMCTable.prototype.selectPage = function(state) {
    var that = this;
    var rows = {};
    this.$dom.find('> tbody > tr:not(.empty)').each(function() {
        rows[this.id] = state ? that.getRowData(this) : null;
    });
    this.updateSelectedRows(rows, state);

    this.selectAll(state, false);
};

EMCTable.prototype.updateSelectedRows = function(rows, state) {
    if (state) {
        this.selectedRows = $.extend(this.selectedRows, rows);
    } else {
        for (var id in rows) {
            delete(this.selectedRows[id]);
        }
    }
};

EMCTable.prototype.getQuery = function(page, data) {
    var query = {
        page: typeof (page) === "number" ? page : 1,
        limit: this.limit
    };

    if (typeof (this.sort) === "number") {
        query.sort = this.sort;
    }

    if (this.$filter.length) {
        query.filter = this.$filter.val();
    }

    if (typeof (data) === "object") {
        query = $.extend(query, data);
    }

    return query;
};

EMCTable.prototype.getRowData = function(tr) {
    var data = $(tr).data();
    delete(data.subtable);
    delete(data.selectable);
    return data;
};

EMCTable.prototype.request = {};

EMCTable.prototype.request.get = function(route, data, dataType) {
    if (typeof (EMCXmlHttpRequest) === "function") {
        return EMCXmlHttpRequest.getInstance().get(route, data);
    }

    var result;
    $.ajax({
        type: 'GET',
        url: route,
        data: data,
        async: false,
        dataType: dataType,
        success: function(response, status, xhr) {
            result = response;
        },
        error: function(xhr) {
            alert('Request Execution Error');
        }
    });

    return result;
};