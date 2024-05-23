$(document).ready(function () {
  ("use strict");

  // course thumbnail image
  $(".thumb-img-input").on("change", function (event) {
    let file = event.target.files[0];
    let reader = new FileReader();

    reader.onload = function (e) {
      $(".uploaded-thumb-img").attr("src", e.target.result);
    };

    reader.readAsDataURL(file);
  });

  // course cover image
  $(".cover-img-input").on("change", function (event) {
    let file = event.target.files[0];
    let reader = new FileReader();

    reader.onload = function (e) {
      $(".uploaded-cover-img").attr("src", e.target.result);
    };

    reader.readAsDataURL(file);
  });

  // course price type
  $('input:radio[name="pricing_type"]').on("change", function () {
    let radioBtnVal = $('input:radio[name="pricing_type"]:checked').val();

    if (radioBtnVal == "premium") {
      $("#price-input").removeClass("d-none");
    } else {
      $("#price-input").addClass("d-none");
    }
  });

  // course form
  $("#courseForm").on("submit", function (e) {
    $(".request-loader").addClass("show");
    e.preventDefault();

    let action = $(this).attr("action");
    let fd = new FormData($(this)[0]);

    $.ajax({
      url: action,
      method: "POST",
      data: fd,
      contentType: false,
      processData: false,
      success: function (data) {
        $(".request-loader").removeClass("show");

        if (data.status == "success") {
          location.reload();
        }
      },
      error: function (error) {
        let errors = ``;

        for (let x in error.responseJSON.errors) {
          errors += `<li>
                <p class="text-danger mb-0">${error.responseJSON.errors[x][0]}</p>
              </li>`;
        }

        $("#courseErrors ul").html(errors);
        $("#courseErrors").show();

        $(".request-loader").removeClass("show");

        $("html, body").animate(
          {
            scrollTop: $("#courseErrors").offset().top - 100,
          },
          1000
        );
      },
    });
  });

  // course's thanks page form
  $("#thanksPageForm").on("submit", function (e) {
    $(".request-loader").addClass("show");
    e.preventDefault();

    let action = $(this).attr("action");
    let fd = new FormData($(this)[0]);

    $.ajax({
      url: action,
      method: "POST",
      data: fd,
      contentType: false,
      processData: false,
      success: function (data) {
        $(".request-loader").removeClass("show");

        if (data.status == "success") {
          location.reload();
        }
      },
      error: function (error) {
        let errors = ``;

        for (let x in error.responseJSON.errors) {
          errors += `<li>
                <p class="text-danger mb-0">${error.responseJSON.errors[x][0]}</p>
              </li>`;
        }

        $("#thanksPageErrors ul").html(errors);
        $("#thanksPageErrors").show();

        $(".request-loader").removeClass("show");

        $("html, body").animate(
          {
            scrollTop: $("#thanksPageErrors").offset().top - 100,
          },
          1000
        );
      },
    });
  });

  // blog form
  $("#blogForm").on("submit", function (e) {
    $(".request-loader").addClass("show");
    e.preventDefault();

    let action = $(this).attr("action");
    let fd = new FormData($(this)[0]);

    $.ajax({
      url: action,
      method: "POST",
      data: fd,
      contentType: false,
      processData: false,
      success: function (data) {
        $(".request-loader").removeClass("show");

        if (data.status == "success") {
          location.reload();
        }
      },
      error: function (error) {
        let errors = ``;

        for (let x in error.responseJSON.errors) {
          errors += `<li>
                <p class="text-danger mb-0">${error.responseJSON.errors[x][0]}</p>
              </li>`;
        }

        $("#blogErrors ul").html(errors);
        $("#blogErrors").show();

        $(".request-loader").removeClass("show");

        $("html, body").animate(
          {
            scrollTop: $("#blogErrors").offset().top - 100,
          },
          1000
        );
      },
    });
  });

  // custom page form
  $("#pageForm").on("submit", function (e) {
    e.preventDefault();

    $(".request-loader").addClass("show");
    if ($(".btn-codeview").hasClass("active")) {
      $(".btn-codeview").trigger("click");
      let action = $("#pageForm").attr("action");
      let fd = new FormData($("#pageForm")[0]);
      $.ajax({
        url: action,
        method: "POST",
        data: fd,
        contentType: false,
        processData: false,
        success: function (data) {
          $(".request-loader").removeClass("show");

          if (data.status == "success") {
            location.reload();
          }
        },
        error: function (error) {
          let errors = ``;

          for (let x in error.responseJSON.errors) {
            errors += `<li>
                <p class="text-danger mb-0">${error.responseJSON.errors[x][0]}</p>
              </li>`;
          }

          $("#pageErrors ul").html(errors);
          $("#pageErrors").show();

          $(".request-loader").removeClass("show");

          $("html, body").animate(
            {
              scrollTop: $("#pageErrors").offset().top - 100,
            },
            1000
          );
        },
      });
    } else {
      let action = $(this).attr("action");
      let fd = new FormData($(this)[0]);

      $.ajax({
        url: action,
        method: "POST",
        data: fd,
        contentType: false,
        processData: false,
        success: function (data) {
          $(".request-loader").removeClass("show");

          if (data.status == "success") {
            location.reload();
          }
        },
        error: function (error) {
          let errors = ``;

          for (let x in error.responseJSON.errors) {
            errors += `<li>
                <p class="text-danger mb-0">${error.responseJSON.errors[x][0]}</p>
              </li>`;
          }

          $("#pageErrors ul").html(errors);
          $("#pageErrors").show();

          $(".request-loader").removeClass("show");

          $("html, body").animate(
            {
              scrollTop: $("#pageErrors").offset().top - 100,
            },
            1000
          );
        },
      });
    }
  });

  // sort course lesson contents
  $("#sort-content").sortable({
    stop: function (event, ui) {
      let sortRoute = "";

      if (sortContentUrl) {
        sortRoute = sortContentUrl;
      }

      $(".request-loader").addClass("show");

      let fd = new FormData();

      $(".ui-state-default").each(function (index) {
        fd.append("ids[]", $(this).data("id"));

        let orderNo = parseInt(index) + 1;
        fd.append("orders[]", orderNo);
      });

      $.ajax({
        url: sortRoute,
        type: "POST",
        data: fd,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (data) {
          $(".request-loader").removeClass("show");
        },
      });
    },
  });

  // change course certificate status (enable/disable)
  $('input:radio[name="certificate_status"]').on("change", function () {
    let radioBtnVal = $('input:radio[name="certificate_status"]:checked').val();

    if (radioBtnVal == 1) {
      $("#certificate-settings").show();
    } else {
      $("#certificate-settings").hide();
    }
  });

  // show or hide input field according to selected ad type
  $(".ad-type").on("change", function () {
    let adType = $(this).val();

    if (adType == "banner") {
      if (!$("#slot-input").hasClass("d-none")) {
        $("#slot-input").addClass("d-none");
      }

      $("#image-input").removeClass("d-none");
      $("#url-input").removeClass("d-none");
    } else {
      if (
        !$("#image-input").hasClass("d-none") &&
        !$("#url-input").hasClass("d-none")
      ) {
        $("#image-input").addClass("d-none");
        $("#url-input").addClass("d-none");
      }

      $("#slot-input").removeClass("d-none");
    }
  });

  $(".edit-ad-type").on("change", function () {
    let adType = $(this).val();

    if (adType == "banner") {
      if (!$("#edit-slot-input").hasClass("d-none")) {
        $("#edit-slot-input").addClass("d-none");
      }

      $("#edit-image-input").removeClass("d-none");
      $("#edit-url-input").removeClass("d-none");
    } else {
      if (
        !$("#edit-image-input").hasClass("d-none") &&
        !$("#edit-url-input").hasClass("d-none")
      ) {
        $("#edit-image-input").addClass("d-none");
        $("#edit-url-input").addClass("d-none");
      }

      $("#edit-slot-input").removeClass("d-none");
    }
  });

  if ($("input[name='quiz_completion']").length > 0) {
    function loadQuizScore() {
      if ($("input[name='quiz_completion']:checked").val() == 1) {
        $("#minScore").show();
      } else {
        $("#minScore").hide();
      }
    }
    loadQuizScore();
    $("input[name='quiz_completion']").on("change", function () {
      loadQuizScore();
    });
  }

  // course price type
  $('input:radio[name="ticket_available_type"]').on("change", function () {
    let radioBtnVal = $(
      'input:radio[name="ticket_available_type"]:checked'
    ).val();

    if (radioBtnVal == "limited") {
      $("#ticket_available").removeClass("d-none");
    } else {
      $("#ticket_available").addClass("d-none");
    }
  });
  // course price type
  $('input:radio[name="max_ticket_buy_type"]').on("change", function () {
    let radioBtnVal = $(
      'input:radio[name="max_ticket_buy_type"]:checked'
    ).val();

    if (radioBtnVal == "limited") {
      $("#max_buy_ticket").removeClass("d-none");
    } else {
      $("#max_buy_ticket").addClass("d-none");
    }
  });

  // ticket pricing_type

  $("body").on("change", "#free_ticket", function () {
    if ($("#free_ticket").prop("checked") == true) {
      $("#ticket-pricing").addClass("d-none");
      $("#early_bird_discount_free").addClass("d-none");
    } else {
      $("#ticket-pricing").removeClass("d-none");
      $("#early_bird_discount_free").removeClass("d-none");
    }
  });

  // event price type
  $('input:radio[name="early_bird_discount_type"]').on("change", function () {
    let radioBtnVal = $(
      'input:radio[name="early_bird_discount_type"]:checked'
    ).val();

    if (radioBtnVal == "enable") {
      $("#early_bird_dicount").removeClass("d-none");
    } else {
      $("#early_bird_dicount").addClass("d-none");
    }
  });
  // event price type
  $('input:radio[name="pricing_type_2"]').on("change", function () {
    let radioBtnVal = $('input:radio[name="pricing_type_2"]:checked').val();

    if (radioBtnVal == "variation") {
      $("#variation_pricing").removeClass("d-none");
      $("#normal_pricing").addClass("d-none");
      $(".hideInvariatinwiseTicket").addClass("d-none");
      $("#early_bird_discount_free").removeClass("d-none");
    } else if (radioBtnVal == "normal") {
      $("#variation_pricing").addClass("d-none");
      $("#normal_pricing").removeClass("d-none");
      $(".hideInvariatinwiseTicket").removeClass("d-none");
      $("#early_bird_discount_free").removeClass("d-none");
    } else {
      $("#variation_pricing").addClass("d-none");
      $("#normal_pricing").addClass("d-none");
      $(".hideInvariatinwiseTicket").removeClass("d-none");
      $("#early_bird_discount_free").addClass("d-none");
    }
  });

  $("thead").on("click", ".addRow", function () {
    var id = Math.random(1, 999999);
    var id = parseInt(id * 100);
    if (guest_checkout_status != 1) {
      var max_ticket_for_customer = `<td>
          <div class="from-group mt-1">
            <input type="checkbox" checked name="v_max_ticket_buy_type[]" value="limited"
              class="max_ticket_buy_type" id="buy_limited_${id}" data-id="${id}">
            <label for="buy_limited_${id}" class="buy_limited_${id} ">Limited'</label>

            <input type="checkbox" name="v_max_ticket_buy_type[]" value="unlimited"
              class="max_ticket_buy_type d-none" id="buy_unlimited_${id}" data-id="${id}">
            <label for="buy_unlimited_${id}"
              class="buy_unlimited_${id} d-none">Unlimited</label>
          </div>

          <div class="form-group" id="input2_${id}">
            <label for="">Max ticket for each customer * </label>
            <input type="text" name="v_max_ticket_buy[]" class="form-control">
          </div>
        </td>`;
    } else {
      var max_ticket_for_customer = `<input type="hidden" name="v_max_ticket_buy_type[]" value="unlimited">
      <input type="hidden" name="v_max_ticket_buy[]" class="form-control">`;
    }
    var tr = `<tr>
        <td>
          ${names}
        </td>
        <td>
          <div class="form-group">
            <label for="">Price (${BaseCTxt}) *</label>
            <input type="text" name="variation_price[]" class="form-control">
          </div>
        </td>
        <td>
          <div class="from-group mt-1">
            <input type="checkbox" checked name="v_ticket_available_type[]" value="limited"
              class="ticket_available_type" id="limited_${id}"
              data-id="${id}">
            <label for="limited_${id}"
              class="limited_${id}">Limited</label>

            <input type="checkbox" name="v_ticket_available_type[]" value="unlimited"
              class="ticket_available_type d-none" id="unlimited_${id}"
              data-id="${id}">
            <label for="unlimited_${id}"
              class="unlimited_${id} d-none">Unlimited</label>

          </div>

          <div class="form-group" id="input_${id}">
            <label for="">Ticket Available * </label>
            <input type="text" name="v_ticket_available[]"
              value="" class="form-control">
          </div>
        </td>
        ${max_ticket_for_customer}
        <td><a href="javascript:void(0)" class="btn btn-danger btn-sm deleteRow" > <i class="fas fa-minus"></i></a></td>
      </tr>`;
    $("tbody").append(tr);
  });

  $("tbody").on("click", ".deleteRow", function () {
    $(this).parent().parent().remove();
  });
  $("tbody").on("click", ".deleteRowAndDB", function () {
    $(".request-loader").addClass("show");

    $.get(
      baseUrl + "/admin/delete-variation/" + $(this).data("id"),
      function (data, status) {
        if (data == "success") {
          $(".request-loader").removeClass("show");
          location.reload();
        }
      }
    );
  });

  $(".eventDateType").on("change", function () {
    let value = $(this).val();
    if (value == "multiple") {
      $("#single_dates").addClass("d-none");
      $("#multiple_dates").removeClass("d-none");
      $(".countDownStatus").addClass("d-none");
    } else {
      $("#single_dates").removeClass("d-none");
      $("#multiple_dates").addClass("d-none");
      $(".countDownStatus").removeClass("d-none");
    }
  });

  // Delegation Type
  $(".delegationType").on("change", function async() {
    $(".select-country-field").addClass("d-none");
    $(".select-state-field").addClass("d-none");
    $("#select_country").val("");
    $("#select_state").val("");
    $(".fieldState").empty();

    const value = $(this).val();
    if (value === "selected") {
      $(".select-type-field").removeClass("d-none");
    } else {
      $(".select-type-field").addClass("d-none");
    }
  });

  $(".selectTypeDelegation").on("change", function () {
    $(".select-country-field").addClass("d-none");
    $(".select-state-field").addClass("d-none");
    $("#select_country").val("");
    $("#select_state").val("");

    const value = $("#select_type").val();

    if (["province", "city/district"].includes(value.toLowerCase())) {
      $(".select-country-field").removeClass("d-none");
    }
  });

  $(".fieldCountry").on("change", function () {
    const valueSelectionType = $("#select_type").val().toLowerCase();
    const baseUrl = $("#base_url").val();
    const getValueCountry = $("#select_country").val();
    let contentOptionFieldState = "";

    $(".fieldState").empty();
    $("#select_state").val("");

    if (valueSelectionType === "city/district") {
      $.ajax({
        url: `${baseUrl}/api/get-state/${getValueCountry}`,
        type: "GET",
        dataType: "json",
        success: function (response) {
          contentOptionFieldState += `<option selected value="">Choose State</option>`;
          if (response.data.length > 0) {
            response.data.map((val) => {
              contentOptionFieldState += `<option value="${val.id}">${val.name}</option>`;
            });
          }
          $(".select-state-field").removeClass("d-none");
          $("#select_state").append(contentOptionFieldState);
        },
        error: function (error) {
          console.log("error:", error);
        },
      });
    } else {
      $(".select-state-field").addClass("d-none");
      contentOptionFieldState += `<option selected value="">Choose State</option>`;
      $("#select_state").append(contentOptionFieldState);
    }
  });

  //add row for event dates
  $("thead").on("click", ".addDateRow", function () {
    var tr = `<tr>
                <td>
                  <div class="form-group">
                    <label for="">Start Date *</label>
                    <input type="date" name="m_start_date[]" class="form-control">
                  </div>
                </td>
                <td>
                  <div class="form-group">
                    <label for="">Start Time *</label>
                    <input type="time" name="m_start_time[]" class="form-control">
                  </div>
                </td>
                <td>
                  <div class="form-group">
                    <label for="">End Date *</label>
                    <input type="date" name="m_end_date[]" class="form-control">
                  </div>
                </td>
                <td>
                  <div class="form-group">
                    <label for="">End Time *</label>
                    <input type="time" name="m_end_time[]" class="form-control">
                  </div>
                </td>
                <td>
                  <a href="javascript:void(0)" class="btn btn-danger deleteDateRow">
                    <i class="fas fa-minus"></i></a>
                </td>
              </tr>`;
    $("tbody").append(tr);
  });

  $("tbody").on("click", ".deleteDateRow", function () {
    $(this).parent().parent().remove();
  });

  $("tbody").on("click", ".deleteDateDbRow", function () {
    $(".request-loader").addClass("show");

    $.get($(this).data("url"), function (data, status) {
      if (data == "success") {
        $(".request-loader").removeClass("show");
        location.reload();
      }
    });
  });
});

$("body").on("click", ".ticket_available_type", function () {
  var id = $(this).attr("data-id");
  var full_id = "input_" + id;

  if ($(this).is(":checked") && $(this).val() == "unlimited") {
    $("#" + full_id).addClass("d-none");
    $("#" + full_id).prop("checked", false);
    $("#limited_" + id).addClass("d-none");
    $(".limited_" + id).addClass("d-none");
  } else if ($(this).not(":checked") && $(this).val() == "limited") {
    $("#" + full_id).addClass("d-none");
    $("#" + full_id).prop("checked", false);
    $("#limited_" + id).addClass("d-none");
    $(".limited_" + id).addClass("d-none");
    $(".unlimited_" + id).removeClass("d-none");
    $("#unlimited_" + id).removeClass("d-none");
    $("#unlimited_" + id).prop("checked", true);
  } else {
    $("#" + full_id).removeClass("d-none");
    $("#limited_" + id).removeClass("d-none");
    $("#limited_" + id).prop("checked", true);
    $(".limited_" + id).removeClass("d-none");
    $(this).addClass("d-none");
    $(this).prop("checked", false);
    $(".unlimited_" + id).addClass("d-none");
  }
});
$("body").on("click", ".max_ticket_buy_type", function () {
  var id = $(this).attr("data-id");
  var full_id = "input2_" + id;

  if ($(this).is(":checked") && $(this).val() == "unlimited") {
    $("#" + full_id).addClass("d-none");
    $("#" + full_id).prop("checked", false);
    $("#buy_limited_" + id).addClass("d-none");
    $(".buy_limited_" + id).addClass("d-none");
  } else if ($(this).not(":checked") && $(this).val() == "limited") {
    $("#" + full_id).addClass("d-none");
    $("#" + full_id).prop("checked", false);
    $("#buy_limited_" + id).addClass("d-none");
    $(".buy_limited_" + id).addClass("d-none");
    $(".buy_unlimited_" + id).removeClass("d-none");
    $("#buy_unlimited_" + id).removeClass("d-none");
    $("#buy_unlimited_" + id).prop("checked", true);
  } else {
    $("#" + full_id).removeClass("d-none");
    $("#buy_limited_" + id).removeClass("d-none");
    $("#buy_limited_" + id).prop("checked", true);
    $(".buy_limited_" + id).removeClass("d-none");
    $(this).addClass("d-none");
    $(this).prop("checked", false);
    $(".buy_unlimited_" + id).addClass("d-none");
  }
});

// competition tournament set Category
$("thead").on("click", ".addSetCategory", function () {
  let id = Math.random(1, 999999);
  id = parseInt(id * 100);
  const competition_categories_value = JSON.parse(
    $("#competition_categories_value").val()
  );
  const competition_class_type_value = JSON.parse(
    $("#competition_class_type_value").val()
  );
  const competition_distance_value = JSON.parse(
    $("#competition_distance_value").val()
  );

  let options_field_competition_category = "";
  competition_categories_value.map((val) => {
    options_field_competition_category += `
    <option value="${val.id}">${val.name}</option>
    `;
  });

  const field_competition_category = `
  <td>
    <div class="form-group">
      <select name="competition_categories[]" id="competition_categories[]" class="form-control">${options_field_competition_category}</select>
    </div>
  </td>
  `;

  let options_field_competition_class_type = "";
  competition_class_type_value.map((val) => {
    options_field_competition_class_type += `
    <option value="${val.id}">${val.name}</option>
    `;
  });

  const field_competition_class_type = `
  <td>
    <div class="form-group">
      <select name="competition_class_type[]" id="competition_class_type[]" class="form-control">${options_field_competition_class_type}</select>
    </div>
  </td>
  `;

  const field_competition_class_name = `
  <td>
   <div class="form-group">
        <input type="text" name="competition_class_name[]" id="competition_class_name[]" value="" class="form-control">
    </div>
  </td>
  `;

  let options_field_competition_distance = "";
  competition_distance_value.map((val) => {
    options_field_competition_distance += `
    <option value="${val.id}">${val.name} Meter</option>
    `;
  });

  const field_competition_distance = `
  <td>
    <div class="form-group">
      <select name="competition_distance[]" id="competition_distance[]" class="form-control">${options_field_competition_distance}</select>
    </div>
  </td>
  `;

  const tr = `<tr>
  ${field_competition_category}
  ${field_competition_class_type}
  ${field_competition_class_name}
  ${field_competition_distance}
  <td class="text-center">
      <a href="javascript:void(0)"
          id="buttonDelete[]"
          class="btn btn-sm btn-danger deleteSetCategory">
          <i class="fas fa-minus"></i></a>
  </td>
  </tr>`;
  $("tbody").append(tr);
});

$("tbody").on("click", ".deleteSetCategory", function () {
  $(this).parent().parent().remove();
});

const handleChooseEventContentLanguageCountry = (code) => {
  const getCountryValue = $(`#${code}_country`).val();
  $(`#${code}_state`).empty();
  $(`#${code}_state`).val("");
  $(`#${code}_city`).empty();
  $(`#${code}_city`).val("");

  let contentOption = "";
  $.ajax({
    url: `${baseUrl}/api/get-state/${getCountryValue}`,
    type: "GET",
    dataType: "json",
    success: function (response) {
      contentOption += `<option selected value="">Choose State</option>`;
      if (response.data.length > 0) {
        response.data.map((val) => {
          contentOption += `<option value="${val.id}">${val.name}</option>`;
        });
      }
      $(`#${code}_state`).append(contentOption);
    },
    error: function (error) {
      contentOption += `<option selected value="">Choose State</option>`;
      $(`#${code}_state`).append(contentOption);
      console.log("error:", error);
    },
  });
};

const handleChooseEventContentLanguageState = (code) => {
  const getCountryValue = $(`#${code}_country`).val();
  const getStateValue = $(`#${code}_state`).val();
  $(`#${code}_city`).empty();
  $(`#${code}_city`).val("");
  let contentOption = "";
  $.ajax({
    url: `${baseUrl}/api/get-city/${getCountryValue}/${getStateValue}`,
    type: "GET",
    dataType: "json",
    success: function (response) {
      contentOption += `<option selected value="">Choose City</option>`;
      if (response.data.length > 0) {
        response.data.map((val) => {
          contentOption += `<option value="${val.id}">${val.name}</option>`;
        });
      }
      $(`#${code}_city`).append(contentOption);
    },
    error: function (error) {
      contentOption += `<option selected value="">Choose City</option>`;
      $(`#${code}_city`).append(contentOption);
      console.log("error:", error);
    },
  });
};
