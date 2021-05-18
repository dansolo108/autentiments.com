var msOptionHexColor = function (config) {
    config = config || {};
    msOptionHexColor.superclass.constructor.call(this, config);
};
Ext.extend(msOptionHexColor, Ext.Component, {
    page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('msoptionhexcolor', msOptionHexColor);

msOptionHexColor = new msOptionHexColor();