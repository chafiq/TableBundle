
/**
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */

function EMCTable(dom, route, subtableRoute, limit) {
    if (EMCTable.caller !== EMCTable.handle) {
        throw new Error("This object cannot be instanciated");
    }

    this.$dom = $(dom);
    this.route = route;
    this.subtableRoute = subtableRoute;
    this.limit = limit;

    this.init();
}

EMCTable.DEFAULT_LIMIT = 10;
EMCTable.EVENT_INIT = 'emc.table.init';
EMCTable.EVENT_SELECTION = 'emc.table.selection';
EMCTable.EVENT_PRE_PAGINATE = 'emc.table.prePaginate';
EMCTable.EVENT_POST_PAGINATE = 'emc.table.postPaginate';

EMCTable.prototype.constructor = EMCTable;

EMCTable.instances = {};

EMCTable.handle = function(dom, route, subtableRoute, limit) {

    if (!(dom instanceof HTMLElement)) {
        throw new Error('EMCTable.handle : dom HTMLElement is required');
    }

    if (typeof (route) !== "string") {
        throw new Error('EMCTable.handle : route string is required');
    }

    if (subtableRoute !== null && typeof (subtableRoute) !== "string") {
        throw new Error('EMCTable.handle : subtableRoute string|null');
    }

    if (typeof (limit) !== "number") {
        throw new Error('EMCTable.handle : limit number is required');
    }

    this.instances[dom.id] = new EMCTable(dom, route, subtableRoute, limit);
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
            that.paginate(1, that.sort);
        }, 500);
    });

    this.$dom.on('click', '> thead > tr > th[data-sort]', function(event) {
        $(this.parentNode).find('> th.sort').removeClass('sort');

        $(this).addClass('sort')
                .toggleClass('dropup');

        var sort = parseInt($(this).data('sort'));
        that.sort = $(this).hasClass('dropup') ? -sort : sort;
        that.paginate(1, that.sort);
    });

    this.$dom.on('change', '> tfoot > tr > td > select', function(event) {
        that.limit = $(this).val();
        that.paginate(1, that.sort);
    });

    this.$dom.on('click', '> tfoot > tr > td > ul.pagination > li > a', function(event) {
        that.paginate($(this).data('page'), that.sort);
    });

    if (this.subtableRoute !== null) {
        this.$dom.on('click', '> tbody > tr[data-subtable=true]', function(event) {
            that.openSubtable(this);
        });
    }

    that.$dom.trigger(EMCTable.EVENT_INIT);
};

EMCTable.prototype.paginate = function(page, sort) {

    this.$dom.trigger(EMCTable.EVENT_PRE_PAGINATE);

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

    delete(html);
    delete($html);

    this.$dom.trigger(EMCTable.EVENT_POST_PAGINATE);
};

EMCTable.prototype.openSubtable = function(tr) {
    if (tr.nextSibling.className === "subtable") {
        if ( !$(tr.nextSibling).is(':visible') ) {
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

    var params = {};
    var data = $tr.data();
    for( var param in data ) {
        if ( param.startsWith('_') ) {
            params[param.substr(1)] = data[param];
        }
    }
    delete(params.subtable);

    var html = EMCXmlHttpRequest.getInstance().get(this.subtableRoute, {params: params, subtable: true}, null, null, 'X-EMC-Table');

    $td.append(html);
};