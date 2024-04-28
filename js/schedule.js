const url = "http://192.168.0.253:8080/api/";
const loginDocument = "login.html";
const indexDocument = "index.html";

function preProcessResponse(data) {
    if (data["type"] == "error" && data["code"] == 15) {
        checkLogin(loginDocument, "")
    }
}

function getToken() {
    var value = localStorage.getItem("token");
    value != null ? value = value : value = sessionStorage.getItem("token");
    return value;
}

async function queryServer(data, type, functionPreCall = preProcessResponse) {
    const token = getToken();
    var tokenArray = {};
    if (token != null) {
        tokenArray = { "token": token };
    }
    const parserData = { ...data, ...tokenArray };
    const requestOptions = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
            json: JSON.stringify(parserData)
        })
    };
    try {
        const response = await fetch(url + type, requestOptions);
        const reply = await response.json();
        functionPreCall(reply);
        return reply;
    } catch (error) {
        console.error('Erro:', error);
    }
}

async function login(username, password, isSession) {
    const response = await queryServer({ "user": username, "password": password, "permanent": isSession }, "login");
    if (response["type"] == "success") {
        if (!isSession) {
            sessionStorage.setItem("token", response["token"]);
        } else {
            localStorage.setItem("token", response["token"]);
        }
        return true;
    } else {
        return false;
    }
}


async function checkLogin(targetFail = "", targetSucefull = "") {
    while (true) {
        const token = getToken();
        if (token == null)
            break;
        const status = await queryServer({}, "info")
        if (status["info"][0]["loginStatus"] == 0) {
            logout(loginDocument);
            break;
        }
        if (targetSucefull != "")
            window.location.href = targetSucefull;
        return true;
    }
    if (targetFail != "")
        window.location.href = targetFail;
    return false;
}

async function logout(targetSucefull = "") {
    const response = await queryServer({}, "logout");
    if (response["type"] == "success") {
        localStorage.removeItem("token");
        sessionStorage.removeItem("token");
        if (targetSucefull != "")
            window.location.href = targetSucefull;
        return true;
    }
    alert("Error logout!")
    return false;
}