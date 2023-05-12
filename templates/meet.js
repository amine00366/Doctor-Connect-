minusButton.addEventListener('click', () => {
    let randomUrl = Math.random();
        const domain = 'meet.jit.si';
        const options = {
            roomName: randomUrl.toString(),
            width: 700,
            height: 700,
            parentNode: document.querySelector('#meet')
        };
        const api = new JitsiMeetExternalAPI(domain, options);

        window.location.href = "https://meet.jit.si/".randomUrl.toString();
    });