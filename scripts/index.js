var jsonArray = new Array();

function ClearList() {
    let sidebarul = document.getElementById("sidebarul");

    while (sidebarul.firstChild) {
        sidebarul.removeChild(sidebarul.lastChild);
    }
}

function HandleJSONResponse() {
    for (let i = 0; i < jsonArray.length; i++) {
        BuildSingleButton(i);
    }

    SwitchShownData(0);
}

function SwitchShownData(index) {
    let textarea = window.frames[0].document.getElementById("json-show");
    const text = JSON.stringify(jsonArray[index], undefined, 4);
    
    textarea.value = text;
}

function LoadFiles(fileList) {
    let url = "php/processing.php";
    const formData = new FormData();

    for (let i = 0; i < fileList.length; i++) {
        let file = fileList[i];

        formData.append('files[]', file);
    }

    fetch(url, {
        method: 'POST',
        body: formData,
    })
    .then ((response) => {
        console.log(response);
        return response.json();
    })
    .then((data) => {
        console.log(data);
        jsonArray = data;
        HandleJSONResponse();
    })
    .catch((error) => {
        console.error(error);
    });
}

function BuildSingleButton(index) {
    let sidebarul = document.getElementById("sidebarul");
    let btn = document.createElement("button");
    btn.innerHTML = "file: " + index;
    btn.onclick = function () {
        SwitchShownData(index);
    }
    
    let li = document.createElement("li");
    li.appendChild(btn);
    
    sidebarul.append(li);
}

window.onload = function () {
    document.getElementById("clear-button").onclick = ClearList;
    document.getElementById("file-input").onchange = function (event) {
        var target = event.target;
        var files = target.files;

        LoadFiles(files);
    }
}