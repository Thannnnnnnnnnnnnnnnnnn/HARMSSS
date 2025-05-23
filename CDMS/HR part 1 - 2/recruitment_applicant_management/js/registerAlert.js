$("#registerBtn").on("click", function () {
  var isValid = true;
  $("input[required]").each(function () {
    if ($(this).val() === "") {
      isValid = false;
      $(this).addClass("border-red-500");
    } else {
      $(this).removeClass("border-red-500");
    }
  });
  if (
    $('input[name="password"]').val() !==
    $('input[name="confirm_password"]').val()
  ) {
    isValid = false;
    $('input[type="password"]').addClass("border-red-500");
    swal.fire({
      icon: "error",
      title: "Oops...",
      text: "Passwords do not match!",
    });
  } else {
    $('input[name="confirm_password"]').removeClass("border-red-500");
  }
  if ($('input[name="password"]').val().length < 8) {
    isValid = false;
    $('input[name="password"]').addClass("border-red-500");
    swal.fire({
      icon: "error",
      title: "Oops...",
      text: "Password must be at least 8 characters long!",
    });
  } else {
    $('input[name="password"]').removeClass("border-red-500");
  }
  if ($('input[name="username"]').val().length < 4) {
    isValid = false;
    $('input[name="username"]').addClass("border-red-500");
    swal.fire({
      icon: "error",
      title: "Oops...",
      text: "Username must be at least 4 characters long!",
    });
  } else {
    $('input[name="username"]').removeClass("border-red-500");
  }
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
