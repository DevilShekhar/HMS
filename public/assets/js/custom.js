/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 * 
 */

"use strict";

$(function () {
  $(".upload-box input[type='file']").each(function () {
    var $input = $(this);
    var $box = $input.closest(".upload-box");
    var $label = $box.find("p").first();

    if (!$label.length) {
      return;
    }

    var defaultLabel = $.trim($label.text());
    $input.attr("data-default-label", defaultLabel);

    $input.on("change", function () {
      var fileName = "";

      if (this.files && this.files.length) {
        fileName = this.files.length === 1 ? this.files[0].name : this.files.length + " files selected";
      }

      $label.text(fileName || defaultLabel);
      $box.toggleClass("has-file", !!fileName);
    });
  });

  var $excelInput = $("#excelUpload");
  var $excelFileName = $("#excelUploadName");

  if ($excelInput.length && $excelFileName.length) {
    $excelInput.on("change", function () {
      var fileName = this.files && this.files.length ? this.files[0].name : "No file selected";
      $excelFileName.text(fileName);
    });
  }

  $("#excelUploadConfirm").on("click", function () {
    if (!$excelInput.length || !$excelInput[0].files || !$excelInput[0].files.length) {
      $excelFileName.text("Please choose a file first");
      return;
    }

    $("#excelUploadModal").modal("hide");
  });
});

