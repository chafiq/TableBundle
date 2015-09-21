
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

    this.$filter = this.$dom.find('thead > tr > td > .input-group > input[name=_filter]');
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

    this.$dom.on('click', '> thead > tr > th[data-sort]', function(event) {
        $(this.parentNode).find('> th.sort').removeClass('sort');

        $(this).addClass('sort')
                .toggleClass('dropup');

        var sort = parseInt($(this).data('sort'));
        that.sort = $(this).hasClass('dropup') ? -sort : sort;
        that.find(1, that.sort);
    });

    this.$dom.on('change', '> tfoot > tr > td > select', function(event) {
        that.limit = $(this).val();
        that.find(1, that.sort);
    });

    this.$dom.on('click', '> tfoot > tr > td > ul.pagination > li > a', function(event) {
        that.find($(this).data('page'), that.sort);
    });

    if (this.selectable) {

        this.$dom.find('> tbody > tr:not(.empty) > td.select_column > input[type=checkbox]').prop('checked', false);

        this.$dom.on('change', '> tbody > tr:not(.empty) > td.select_column > input[type=checkbox]', function(event) {
            that.select(this);
        });

        this.$dom.on(EMCTable.EVENT_CHANGE, function(event) {
            var selectedRows = Object.keys(that.selectedRows)
                    .map(function(id) {
                        return '> tbody > tr#' + id + ' > td.select_column > input[type=checkbox]';
                    })
                    .join(', ');
            that.$dom.find(selectedRows).prop("checked", true);
        });
    }

    if (this.subtableRoute !== null) {
        this.$dom.on(EMCTable.EVENT_SUBTABLE, function(event, table) {
            if (typeof ($.fn.selectpicker) === "function") {
                $(table).find('> tfoot > tr > td > select').selectpicker();
            }
        });
    }
    
    if (this.subtableRoute !== null || this.selectable) {
        this.$dom.on('click', '> tbody > tr[data-subtable]', function(event) {
            if ((event.target.nodeName === "INPUT" && event.target.parentNode.className === 'select_column')
                    || event.target.nodeName === "A"
                    || event.target.nodeName === "BUTTON"
                    ) {
                return;
            }

            if (that.selectable && event.ctrlKey) {
                var input = $(this).find('> td.select_column > input[type=checkbox]').get(0);
                input.checked = !input.checked;
                that.select(input);
            } else if (!event.ctrlKey) {
                that.openSubtable(this);
            }
            event.preventDefault();
            event.stopPropagation();
        });
    }

    that.$dom.trigger(EMCTable.EVENT_INIT);
};

EMCTable.prototype.find = function(page, sort) {

    this.$dom.trigger(EMCTable.EVENT_FIND);

    var data = {
        _page: page,
        _limit: this.limit
    };

    if (typeof (sort) === "number") {
        data._sort = sort;
    }

    if (this.$filter.length) {
        data._filter = this.$filter.val();
    }

    var height = this.$dom.height();

    var html = EMCXmlHttpRequest.getInstance().get(this.route, data, null, null, 'X-EMC-Table');

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
            .attr('colspan', tr.childElementCount);

    var $tr = $(tr);
    $tr.after(
            $(document.createElement('tr'))
            .addClass('subtable')
            .append($td)
            );

    var data = $tr.data();

    var html = EMCXmlHttpRequest.getInstance().get(this.subtableRoute, {params: data.subtable, subtable: true}, null, null, 'X-EMC-Table');

    $td.append(html);

    this.$dom.trigger(EMCTable.EVENT_CHANGE);
    this.$dom.trigger(EMCTable.EVENT_SUBTABLE, [$td.find('> table').get(0)]);
};

EMCTable.prototype.select = function(input) {
    var tr = input.parentNode.parentNode;
    var data = $(tr).data();
    delete(data.subtable);

    $(tr).toggleClass('info', input.checked);
    if (!input.checked) {
        delete(this.selectedRows[ tr.id ]);
        this.$dom.trigger(EMCTable.EVENT_SELECT, [data, false, this.selectedRows]);
        return;
    }

    this.selectedRows[ tr.id ] = data;
    this.$dom.trigger(EMCTable.EVENT_SELECT, [data, true, this.selectedRows]);
};