$("#registerBtn").on("click", function () {
  let isValid = true;
  $("input[required]").each(function () {
    if ($(this).val() === "") {
      isValid = false;
      $(this).addClass("border-red-500");
    } else {
      $(this).removeClass("border-red-500");
    }
  });
  if (isValid) {
    swal.fire({
      icon: "success",
      title: "Success!",
      text: "Registration successful!",
    });
    $("form").submit();
    $(this).prop("disabled", true);
    $(this).text("Registering...");
  }
});
