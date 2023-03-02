Ext.Loader.load([
    MODx.config.assets_url + 'components/msmulticurrency/js/mgr/misc/strftime-min-1.3.js'
]);
var MsMC = function (config) {
    config = config || {};
    MsMC.superclass.constructor.call(this, config);
};
Ext.extend(MsMC, Ext.Component, {
    page: {},
    window: {},
    grid: {},
    tree: {},
    panel: {},
    combo: {},
    config: {},
    view: {},
    extra: {},
    utils: {},
    connector_url: ''

});
Ext.reg('MsMC', MsMC);

Ext.override(Ext.form.FieldSet, {
    getState: function () {
        return {collapsed: this.collapsed};
    }
});

Ext.override(Ext.form.ComboBox, {
    getSelectedRecord: function () {
        return this.findRecord(this.valueField || this.displayField, this.getValue());
    },
    getSelectedIndex: function () {
        return this.store.indexOf(this.getSelectedRecord());
    }
});

MsMC = new MsMC();

MsMC.utils.formatDate = function (string) {
    if (string && string != '0000-00-00 00:00:00' && string != '-1-11-30 00:00:00' && string != 0) {
        var date = /^[0-9]+$/.test(string)
            ? new Date(string * 1000)
            : new Date(string.replace(/(\d+)-(\d+)-(\d+)/, '$2/$3/$1'));
        return date.strftime(MsMC.config['date_format']);
    } else {
        return '&nbsp;';
    }
};

MsMC.utils.renderActions = function (value, props, row) {
    var res = [];
    var cls, icon, title, action, item = '';
    for (var i in row.data.actions) {
        if (!row.data.actions.hasOwnProperty(i)) {
            continue;
        }
        var a = row.data.actions[i];
        if (!a['button']) {
            continue;
        }

        icon = a['icon'] ? a['icon'] : '';
        if (typeof (a['cls']) == 'object') {
            if (typeof (a['cls']['button']) != 'undefined') {
                icon += ' ' + a['cls']['button'];
            }
        } else {
            cls = a['cls'] ? a['cls'] : '';
        }
        action = a['action'] ? a['action'] : '';
        title = a['title'] ? a['title'] : '';

        item = String.format(
            '<li class="{0}"><button class="btn btn-default {1}" action="{2}" title="{3}"></button></li>',
            cls, icon, action, title
        );

        res.push(item);
    }

    return String.format(
        '<ul class="msmc-row-actions">{0}</ul>',
        res.join('')
    );
};

MsMC.utils.getMenu = function (actions, grid, selected) {
    var menu = [];
    var cls, icon, title, action = '';

    var has_delete = false;
    for (var i in actions) {
        if (!actions.hasOwnProperty(i)) {
            continue;
        }

        var a = actions[i];
        if (!a['menu']) {
            if (a == '-') {
                menu.push('-');
            }
            continue;
        } else if (menu.length > 0 && !has_delete && (/^remove/i.test(a['action']) || /^delete/i.test(a['action']))) {
            menu.push('-');
            has_delete = true;
        }

        if (selected.length > 1) {
            if (!a['multiple']) {
                continue;
            } else if (typeof (a['multiple']) == 'string') {
                a['title'] = a['multiple'];
            }
        }

        icon = a['icon'] ? a['icon'] : '';
        if (typeof (a['cls']) == 'object') {
            if (typeof (a['cls']['menu']) != 'undefined') {
                icon += ' ' + a['cls']['menu'];
            }
        } else {
            cls = a['cls'] ? a['cls'] : '';
        }
        title = a['title'] ? a['title'] : a['title'];
        action = a['action'] ? grid[a['action']] : '';

        menu.push({
            handler: action,
            text: String.format(
                '<span class="{0}"><i class="x-menu-item-icon {1}"></i>{2}</span>',
                cls, icon, title
            ),
            scope: grid
        });
    }

    return menu;
};
MsMC.utils.roundNumeric = function (number, precision) {
    number = number.toString().replace(',', '.');
    var tmp = number.split('.');
    if (tmp.length == 1) return number;
    return tmp[0] + '.' + tmp[1].substr(0, parseInt(precision));
};

MsMC.utils.numberFormat = function (number, decimals, dec_point, thousands_sep) {
    // original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // bugfix by: Michael White (http://crestidg.com)
    var i, j, kw, kd, km;

    // input sanitation & defaults
    if (isNaN(decimals = Math.abs(decimals))) {
        decimals = 2;
    }
    if (dec_point == undefined) {
        dec_point = ',';
    }
    if (thousands_sep == undefined) {
        thousands_sep = '.';
    }


    i = parseInt(number = (+number || 0).toFixed(decimals)) + '';


    if ((j = i.length) > 3) {
        j = j % 3;
    } else {
        j = 0;
    }


    km = j
        ? i.substr(0, j) + thousands_sep
        : '';
    kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
    kd = (decimals
        ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, '0').slice(2)
        : '');
    return km + kw + kd;
};

MsMC.utils.isEmpty = function (obj) {
    if (!obj || Object.keys(obj).length === 0) {
        return true;
    }
    return false;
};