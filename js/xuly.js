$(document).ready(function () {
  $("#login").validate({
    rules: {
      username: {
        required: true,
      },
      pwd: {
        required: true,
        minlength: 6,
      },
    },
    messages: {
      username: {
        required: "Enter username",
      },
      pwd: {
        required: "Enter password",
        minlength: "Password has at least 6 characters",
      },
    },
  });
});
