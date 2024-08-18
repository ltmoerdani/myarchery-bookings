$("#NextFormOrder").on("click", function (e) {
  $(e.target).attr("disabled", true);
  $(".request-loader").addClass("show");

  const d = new Date();
  let time = d.getTime();

  let eventForm = document.getElementById("eventForm");
  let fd = new FormData(eventForm);
  const checkoutID = time + getRandomNumber(999, 99999) + $("#event_id").val();
  fd.append("checkoutId", checkoutID);

  let url = $("#eventForm").attr("action");
  let method = $("#eventForm").attr("method");

  $.ajax({
    url: url,
    method: method,
    data: fd,
    contentType: false,
    processData: false,
    success: function (response) {
      location.href = `${baseUrl}/process-form-order-tournament?checkoutID=${checkoutID}`;
      $(e.target).attr("disabled", false);
      $(".request-loader").removeClass("show");
    },
    error: function (error) {
      $(e.target).attr("disabled", false);
      $(".request-loader").removeClass("show");
      if (!error.status) {
        return toastr["error"](error);
      }

      if (error.status === 400) {
        return toastr["warning"](error.responseJSON.message);
      }

      if (error.status === 403) {
        return toastr["error"](error.responseJSON.message);
      }

      if (error.status === 404) {
        return toastr["error"](error.responseJSON.message);
      }
    },
  });
});
