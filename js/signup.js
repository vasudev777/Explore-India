function validatereg(form) {

    pass = "/^\w+([A-Z]?\w+)*@\w+([a-z]?\w+)*(\.\w[0-9]?\w+)*(\.\w{@,#,$,&,*})";

    var p1 = form.pass1.value;
    var p2 = form.pass2.value;
    var email1 = form.email.value;
    if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email1)) {

    } else {
        alert("You have entered an invalid email address!");
    }
    if (p1.length == " " || p2.length == " ") {
        alert("Enter both the passwords!!");
    }
    if (p1.length < 8) {
        alert("password must have more then 8 character !!!");
        return false;

    }

    if (p1.length == 0) {
        alert("You Must Enter Password !!!");
        return false;

    }
    if (p2.length == 0) {
        alert("You Must Enter a  Confirm-password !!!");
        return false;

    }

    if (p1 != p2) {
        alert("Your password does not match,re-enter both password!");
    }

}


    	