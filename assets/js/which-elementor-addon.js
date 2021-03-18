;(function($, wea) {
    'use strict';

    $(window).on('elementor/frontend/init', function() {
        var isEditMode = elementorFrontend.config.environmentMode.edit,
            isEnabledOn = function(side) {
                return wea && wea.settings && wea.settings.enableOn.indexOf(side) !== -1;
            },
            classes = [
                'wea',
                'wea--labelPosition-' + wea.settings.labelPosition,
                'wea--showLabelOn-' + wea.settings.showLabelOn,
            ];

        if ( ! isEnabledOn('editor') && isEditMode ) {
            return;
        }

        if ( ! isEnabledOn('frontend') && ! isEditMode ) {
            return;
        }

        elementorFrontend.elements.$body.addClass(classes.join(' '));

        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
            var widgetType = $scope.data('widget_type').split('.')[0],
                widgetData = wea.widgetPluginMap[widgetType] || {},
                pluginName = widgetData.plugin || '',
                widgetName = widgetData.widget || '',
                $d = $('<span class="wea__label">' + (wea.settings.showWidgetName ? pluginName + ' (' + widgetName + ')' : pluginName) + '</span>');

            if (pluginName) {
                $scope.append($d);
            }
        });
    });
}(jQuery, whichElementorAddon));
