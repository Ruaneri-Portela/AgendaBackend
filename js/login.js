checkLogin("", indexDocument);
const btn = document.getElementById("btnLogin");
const username = document.getElementById("username");
const password = document.getElementById("password");
const isSession = document.getElementById("keepConnected");
async function btnAction() {
    if (username.value == "" && password.value == "") {
        alert("Please enter your username and password!");
        return;
    }
    if (await login(username.value, password.value, isSession.checked)) {
        window.location.href = indexDocument;
    } else {
        alert("Incorrect username or password!");
    }

}
btn.addEventListener("click", btnAction);
