checkLogin("", "./index.html");
const btn = document.getElementById("btnLogin");
const username = document.getElementById("username");
const password = document.getElementById("password");
const isSession = document.getElementById("keepConnected");
async function btnAction() {
    if (username.value == "" && password.value == "") {
        alert("Please enter your username and password!");
        return;
    }
    const response = await querySchedule({ "user": username.value, "password": password.value, "permanent": isSession.checked }, "login");
    if (response["type"] == "success") {
        if (!isSession.checked) {
            sessionStorage.setItem("token", response["token"]);
        } else {
            localStorage.setItem("token", response["token"]);
        }
        alert("Login successful");
        window.location.href = "index.html";
        return;
    } else {
        alert("Login failed");
    }
}
btn.addEventListener("click", btnAction);
