$(document).ready(function () {
    if (typeof msFavorites != 'undefined' && typeof miniShop2 != 'undefined') {
        msFavorites.addMethodAction('success', 'name_action', function (r) {
            miniShop2.Message.initialize();

            var self = this;
            if (self.data && self.data.method == 'add') {
                miniShop2.Message.success('add');
            }
            if (self.data && self.data.method == 'remove') {
                miniShop2.Message.info('remove');
            }
        });
    }
});