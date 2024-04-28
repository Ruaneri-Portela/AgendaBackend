checkLogin(loginDocument, "");

const btnLogout = document.getElementById("logout");
btnLogout.addEventListener("click", function () {
    logout(loginDocument)
});

async function getInfo() {
    const data = await queryServer({}, "info");
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
    const response = await queryServer({ "id": id }, "delete");
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
            response = await queryServer({ "room": room, "timestampStart": start, "timestampEnd": end, "userId": user, "reason": reason }, "create");
            fail = false;
            break;
        }
        if (mode == "edit" && !(id == "")) {
            response = await queryServer({ "id": id, "room": room, "timestampStart": start, "timestampEnd": end, "userId": user, "reason": reason }, "edit");
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

function fillButtonTableElement(id) {
    const buttons = ["Delete", "Edit"];
    for (var i = 0; i < buttons.length; i++) {
        id.appendChild(document.createElement("button")).appendChild(document.createTextNode(buttons[i]));
    }
    id.children[0].onclick = function () {
        deleteScheduleBtn(id.parentElement.children[0].innerText);
    }
    id.children[1].onclick = function () {
        moveElementToEdit(id.parentElement);
    }
}

function createNewScheduleInput(id, elments = Array("#", "", "", "", "", "")) {
    var tr = document.createElement("tr");
    var td = [];
    var input = [];
    var button = [];
    for (var i = 0; i < 7; i++) {
        td.push(document.createElement("td"));
    }
    for (var i = 0; i < 5; i++) {
        input.push(document.createElement("input"));
    }
    for (var i = 0; i < 2; i++) {
        button.push(document.createElement("button"));
    }
    td[0].appendChild(document.createTextNode(elments[0]));
    input[0].setAttribute("type", "number");
    input[1].setAttribute("type", "datetime-local");
    input[2].setAttribute("type", "datetime-local");
    input[3].setAttribute("type", "number");
    input[4].setAttribute("type", "text");
    for (var i = 0; i < input.length; i++) {
        input[i].value = elments[i + 1];
    }
    button[0].appendChild(document.createTextNode("Create"));
    button[0].onclick = function () { scheduleBtn("", input[0].value, input[1].value, input[2].value, input[3].value, input[4].value, "add") };
    button[1].appendChild(document.createTextNode("Clean"));
    button[1].onclick = function () {
        for (var i = 0; i < input.length; i++) {
            input[i].value = "";
        }
    }
    for (var i = 0; i < 5; i++) {
        td[i + 1].appendChild(input[i]);
    }
    for (var i = 0; i < button.length; i++) {
        tr.appendChild(td[6]).appendChild(button[i]);
    }
    for (var i = 0; i < td.length; i++) {
        tr.appendChild(td[i]);
    }
    id.appendChild(tr);
}

function moveElementToEdit(id) {
    const tr = id.parentElement.parentElement.children[2].children[0];
    if (tr.children[0].innerText != "#") {
        for (var i = 0; i < id.parentElement.childElementCount; i++) {
            id.parentElement.children[i].style.backgroundColor = "white";
            id.parentElement.children[i].lastElementChild.children[0].disabled = false;
            id.parentElement.children[i].lastElementChild.children[1].disabled = false;
        }
    }
    id.style.backgroundColor = "yellow";
    id.lastElementChild.children[0].disabled = true;
    id.lastElementChild.children[1].disabled = true;
    tr.children[0].innerText = id.children[0].innerText;
    tr.children[1].children[0].value = parseInt(id.children[1].innerText);
    tr.children[2].children[0].value = id.children[2].innerText.replace(" ", "T").slice(0, -3);
    tr.children[3].children[0].value = id.children[3].innerText.replace(" ", "T").slice(0, -3);
    tr.children[4].children[0].value = parseInt(id.children[4].innerText);
    tr.children[5].children[0].value = id.children[5].innerText;
    tr.children[6].children[0].innerText = "Update";
    tr.children[6].children[0].onclick = function () {
        scheduleBtn(tr.children[0].innerText, tr.children[1].children[0].value, tr.children[2].children[0].value, tr.children[3].children[0].value, tr.children[4].children[0].value, tr.children[5].children[0].value, "edit");
        genTable();
    }
    tr.children[6].children[1].onclick = function () {
        genTable();
    }
    tr.children[6].children[1].innerText = "Cancel";
}

async function genTable() {
    var id = document.getElementById("apptable");
    var table = document.createElement("table");
    var thead = document.createElement("thead");
    var tbody = document.createElement("tbody");
    var tfoot = document.createElement("tfoot");
    var tr = document.createElement("tr");
    var labels = ["ID", "Room", "Start", "End", "User", "Reason", "Action"];
    var labelsJson = ["id", "room", "timestampStart", "timestampEnd", "userId", "reason"];
    for (var i = 0; i < labels.length; i++) {
        tr.appendChild(document.createElement("th")).appendChild(document.createTextNode(labels[i]));
    }
    thead.appendChild(tr);
    var schedules = await queryServer({ "room": "*", "timestampStart": "2024-01-01 00:00:00", "timestampEnd": "*", "userId": "*", }, "search");
    if (schedules["type"] != "error") {
        var list = schedules["list"]["result"];
        for (element in list) {
            var tr = document.createElement("tr");
            for (var i = 0; i < labelsJson.length; i++) {
                tr.appendChild(document.createElement("td")).appendChild(document.createTextNode(list[element][labelsJson[i]]));
            }
            var elementTd = document.createElement("td");
            fillButtonTableElement(elementTd);
            tr.appendChild(elementTd);
            tbody.appendChild(tr);
        }
    } else {
        var tr = document.createElement("tr");
        tr.appendChild(document.createElement("td")).appendChild(document.createTextNode("No schedules found"));
        tbody.appendChild(tr);
    }
    createNewScheduleInput(tfoot);
    table.appendChild(thead);
    table.appendChild(tbody);
    table.appendChild(tfoot);
    id.innerHTML = "";
    id.appendChild(table);
}

getInfo();
genTable();
