$(document).ready(function () {
  $("#main-form").on("submit", function (e) {
    e.preventDefault();

    let form_data = {
      firstname: $("#firstname").val(),
      lastname: $("#lastname").val(),
      email: $("#email").val(),
      password: $("#password").val(),
      repeat_password: $("#repeat_password").val(),
    };

    $.ajax({
      method: "post",
      url: "post.php",
      data: form_data,
      dataType: "json",
      //   encode: true,
    }).done(function (data) {
      console.log(data);

      if (!data.success) {
        $(".error_msg").remove();
        if (data.errors.firstname) {
          $("#fname_wrap").addClass("error");
          $("#fname_wrap").append(`<span class='error_msg'> ${data.errors.firstname} </span>`);
        }

        if (data.errors.email) {
          $("#email_wrap").addClass("error");
          $("#email_wrap").append(`<span class='error_msg'> ${data.errors.email} </span>`);
        }

        if (data.errors.unique) {
          $("#email_wrap").addClass("error");
          $("#email_wrap").append(`<span class='error_msg'> ${data.errors.unique} </span>`);
        }

        if (data.errors.password) {
          $("#pass_wrap").addClass("error");
          $("#pass_wrap").append(`<span class='error_msg'> ${data.errors.password} </span>`);
        }

        if (data.errors.repeat_password) {
          $("#rpass_wrap").addClass("error");
          $("#rpass_wrap").append(`<span class='error_msg'> ${data.errors.repeat_password} </span>`);
        }
      } else {
        $(".main-content").html('<div class="success"> <h2>Вы успешно зарегестрированы!</h2> </div>');
      }
    });
  });
});
