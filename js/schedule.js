async function querySchedule(data, type) {
    var value = localStorage.getItem("token");
    value != null ? value = value : value = sessionStorage.getItem("token");
    const parserData = { ...data, "token": value };
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
        const response = await fetch("http://192.168.0.253:8080/api/" + type, requestOptions);
        const reply = await response.json();
        if (reply["type"] == "error" && reply["code"] == 15) {
            checkLogin("login.html", "")
        }
        return reply;
    } catch (error) {
        console.error('Erro:', error);
    }
}

async function getInfo() {
    const data = await querySchedule({}, "info");
    var info = document.getElementById("footer");
    var text = "<br>Server Info:<br><p>";
    for (element in data["info"]) {
        if (!(element == '0')) {
            text = text + element + ": " + data["info"][element] + "<br>";
        }
    }
    for (element in data["info"][0]) {
        text = text + element + ": " + data["info"][0][element] + "<br>";
    }
    text = text + "</p>";
    info.innerHTML = text;
}

async function deleteScheduleBtn(id) {
    const response = await querySchedule({ "id": id }, "delete");
    if (response["type"] == "success") {
        alert("Schedule deleted successfully");
        genTable();
        return;
    }
    alert("Error deleting schedule\n" + response["msg"] + "\nPlease try again");
}

async function scheduleBtn(id, room, start, end, user, reason, mode) {
    var fail = true;
    var response = null;
    while (true) {
        if (room == "" || start == "" || end == "" || reason == "") {
            break;
        }
        if (mode == "add" && !(user == "")) {
            response = await querySchedule({ "room": room, "timestampStart": start, "timestampEnd": end, "userId": user, "reason": reason }, "create");
            fail = false;
            break;
        }
        if (mode == "edit" && !(id == "")) {
            response = await querySchedule({ "id": id, "room": room, "timestampStart": start, "timestampEnd": end, "userId": user, "reason": reason }, "edit");
            fail = false;
            break;
        }
        break;
    }
    if (fail) {
        alert("Please fill all fields");
        return;
    }
    if (response != null && response["type"] == "success") {
        alert("Schedule created successfully");
        genTable();
        return;
    }
    alert("Error creating schedule\n" + response["msg"] + "\nPlease try again");
}

async function checkLogin(targetFail = "", targetSucefull = "") {
    while (true) {
        var value = localStorage.getItem("token");
        value != null ? value = value : value = sessionStorage.getItem("token");
        if (value == null) {
            break;
        }
        var status = await querySchedule({ "token": value }, "info")
        if (status["info"][0]["loginStatus"] == 0) {
            logout();
            break;
        }
        if (targetSucefull != "")
            window.location.href = targetSucefull;
        return;
    }
    if (targetFail != "")
        window.location.href = targetFail;
}

function logout() {
    localStorage.removeItem("token");
    sessionStorage.removeItem("token");
    window.location.href = "login.html";
}
