jQuery(function ($) {
    "use strict";
    $(document).ready(function () {
        var $preInstallationTab = $("#pre-installation-tab"),
            $configurationTab = $("#configuration-tab");

        $(".form-next").on('click', function () {
            if ($preInstallationTab.hasClass("active")) {
                $preInstallationTab.removeClass("active");
                $configurationTab.addClass("active");
                $("#pre-installation").find("i").removeClass("fa-circle-o").addClass("fa-check-circle");
                $("#configuration").addClass("active");
                $("#host").focus();
            }
        });

    });
});
