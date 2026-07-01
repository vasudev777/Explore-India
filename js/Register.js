function validateForm() {
    x = document.forms["reg"]["fname"].value;
    p = document.forms["reg"]["pass"].value;
    cp = document.forms["reg"]["cpass"].value;
    e = document.forms["reg"]["email"].value;


    if (x == "") {
        var text = "Name Is Compulsory";
        document.getElementById("demo").innerHTML = text;
        return false;
    }
    if (e == "") {
        var text = "Enter Email";
        document.getElementById("demo").innerHTML = text;
        return false;
    }
    if (p == "") {
        var text = "Enter The Password";
        document.getElementById("demo").innerHTML = text;
        return false;
    }
    if (cp == "") {
        var text = "Enter The Confirm Password";
        document.getElementById("demo").innerHTML = text;
        return false;
    }
    if (p.length <= 8) {
        var text = "Enter Password More Than 8 Values";
        document.getElementById("demo").innerHTML = text;
        return false;

    }
    if (cp.length <= 8) {
        var text = "Enter Confirm Password  More Than 8 Values";
        document.getElementById("demo").innerHTML = text;
        return false;

    }
    if (p !== cp) {
        var text = "Invalid Password"
        document.getElementById("demo").innerHTML = text;
        return false;
    }
    {
        // var text = "Valid Password"
        document.getElementById("demo").innerHTML = text;
        return true;
    }

}