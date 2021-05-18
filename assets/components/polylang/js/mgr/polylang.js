var Polylang = function (config) {
    config = config || {};
    Polylang.superclass.constructor.call(this, config);
};
Ext.extend(Polylang, Ext.Component, {
    page: {},
    window: {},
    grid: {},
    tree: {},
    panel: {},
    combo: {},
    field: {},
    config: {},
    view: {},
    extra: {},
    utils: {},
    connector_url: ''

});
Ext.reg('Polylang', Polylang);

Ext.apply(Ext, {
    isObject: function(v) {
        return v && Object.prototype.toString.call(v) == '[object Object]';
    }
});

Ext.override(Ext.form.FieldSet, {
    getState: function () {
        return {collapsed: this.collapsed};
    }
});

Ext.override(Ext.form.BasicForm, {
    clearDirty: function (nodeToRecurse) {
        nodeToRecurse = nodeToRecurse || this;
        if (!nodeToRecurse.items.each) return;
        nodeToRecurse.items.each(function (f) {
            if (!f.getValue) return;

            if (f.items) {
                this.clearDirty(f);
            } else if (f.originalValue != f.getValue()) {
                f.originalValue = f.getValue();
            }
        }, this);
    }
});

Ext.override(Ext.form.ComboBox, {
    getSelectedRecord: function () {
        var record = this.findRecord(this.valueField || this.displayField, this.getValue());
        return record ? record : null;
    },
    getSelectedIndex: function () {
        var record = this.getSelectedRecord();
        return (this.store.indexOf(record));
    },
    setTriggerDisable: function (disabled, trigger) {
        if (!this.trigger) return;
        var btn = this.trigger.select('.x-form-trigger.x-field-combo-btn-' + trigger, trigger).item(0);
        if (!btn) return;
        if (disabled) {
            btn.addClass('trigger-disable');
        } else {
            btn.removeClass('trigger-disable');
        }
    },
    isTriggerDisable: function (trigger) {
        var btn = this.trigger.select('.x-form-trigger.x-field-combo-btn-' + trigger, trigger).item(0);
        if (!btn) return true;
        return btn.hasClass('trigger-disable');
    }
});

Ext.override(Ext.form.Field, {
    getLabel: function() {
        return this.el.up('.x-form-element').prev('label');
    }
});

Polylang = new Polylang();

Polylang.utils.isEmpty = function (obj) {
    if (!obj || Object.keys(obj).length === 0) {
        return true;
    }
    return false;
};

Polylang.utils.formatDate = function (string) {
    if (string && string != '0000-00-00 00:00:00' && string != '-1-11-30 00:00:00' && string != 0) {
        var date = /^[0-9]+$/.test(string)
            ? new Date(string * 1000)
            : new Date(string.replace(/(\d+)-(\d+)-(\d+)/, '$2/$3/$1'));
        return date.strftime(Polylang.config['date_format'] || '%d.%m.%y <span class="gray">%H:%M</span>');
    } else {
        return '&nbsp;';
    }
};

Polylang.utils.renderActions = function (value, props, row) {
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
        '<ul class="polylang-row-actions">{0}</ul>',
        res.join('')
    );
};

Polylang.utils.getMenu = function (actions, grid, selected) {
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

Polylang.userLink = function (value, id, blank) {
    if (!value) {
        return '';
    } else if (!id) {
        return value;
    }

    return String.format(
        '<a href="?a=security/user/update&id={0}" class="ms2-link" target="{1}">{2}</a>',
        id,
        (blank ? '_blank' : '_self'),
        value
    );
};

Polylang.utils.resourceLink = function (value, id, blank) {
    if (!value) {
        return '';
    } else if (!id) {
        return value;
    }

    return String.format(
        '<a href="index.php?a=resource/update&id={0}" class="polylang-link" target="{1}">{2}</a>',
        id,
        (blank ? '_blank' : '_self'),
        value
    );
};

Polylang.utils.renderBoolean = function (value) {
    var color, text;
    if (value == 0 || value == false || value == undefined) {
        color = 'red';
        text = _('no');
    } else {
        color = 'green';
        text = _('yes');
    }

    return String.format('<span class="{0}">{1}</span>', color, text);
};
