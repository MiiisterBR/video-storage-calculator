/* getChromaFactor() returns a multiplier for different chroma subsampling patterns.
     4:2:0 is baseline = 1, others are approximate. */
function getChromaFactor(chroma) {
    switch (chroma) {
        case "4:4:4":
            return 2;
        case "4:2:2":
            return 1.5;
        case "4:2:0":
            return 1;
        case "4:4:2":
            return 1.8;
        case "4:1:1":
            return 0.75;
        default:
            return 1;
    }
}

// Global toggle for proxy
let proxyEnabled = false;

function initializePage() {
    openTab("main");
    document.getElementById("proxyTabBtn").style.display = "none";
    updateSize();
}

function toggleProxy() {
    proxyEnabled = !proxyEnabled;
    const proxyTabBtn = document.getElementById("proxyTabBtn");
    const toggleBtn = document.getElementById("toggleProxyBtn");

    if (proxyEnabled) {
        proxyTabBtn.style.display = "inline-block";
        toggleBtn.textContent = "Disable Proxy";
    } else {
        proxyTabBtn.style.display = "none";
        openTab("main");
        toggleBtn.textContent = "Enable Proxy";
    }
    updateSize();
}

function openTab(tabName) {
    const mainTab = document.getElementById("mainTab");
    const proxyTab = document.getElementById("proxyTab");
    const mainTabBtn = document.getElementById("mainTabBtn");
    const proxyTabBtn = document.getElementById("proxyTabBtn");

    if (tabName === "main") {
        mainTab.style.display = "block";
        proxyTab.style.display = "none";

        mainTabBtn.classList.add("bg-blue-100", "text-blue-600", "border-blue-600");
        mainTabBtn.classList.remove("bg-gray-200");

        proxyTabBtn.classList.remove("bg-blue-100", "text-blue-600", "border-blue-600");
        proxyTabBtn.classList.add("bg-gray-200");
    } else {
        if (!proxyEnabled) {
            alert("Proxy is disabled. Please enable it first.");
            return;
        }
        mainTab.style.display = "none";
        proxyTab.style.display = "block";

        proxyTabBtn.classList.add("bg-blue-100", "text-blue-600", "border-blue-600");
        proxyTabBtn.classList.remove("bg-gray-200");

        mainTabBtn.classList.remove("bg-blue-100", "text-blue-600", "border-blue-600");
        mainTabBtn.classList.add("bg-gray-200");
    }
    updateSize();
}

// Keep durations in sync
function syncDurationFromMain() {
    const val = document.getElementById("durationMain").value;
    document.getElementById("durationProxy").value = val;
    updateSize();
}

function syncDurationFromProxy() {
    const val = document.getElementById("durationProxy").value;
    document.getElementById("durationMain").value = val;
    updateSize();
}

// Set resolution depending on the active tab
function setResolution(width, height) {
    if (document.getElementById("mainTab").style.display === "block") {
        document.getElementById("widthMain").value = width;
        document.getElementById("heightMain").value = height;
    } else {
        document.getElementById("widthProxy").value = width;
        document.getElementById("heightProxy").value = height;
    }
    updateSize();
}

/*
  calculateMain()
  1) We read all fields: width, height, colorDepth, framerate, chroma, plus userBitrate, etc.
  2) We'll do a partial exponent approach to scale the user's entered "bitrateMain"
     according to resolution, bit depth, frame rate, and chroma.
  3) Then we apply multipliers for codec, LOG, HDR, All-Intra.
  4) We add audio.
  5) Return total MB/GB over the chosen duration.
*/
function calculateMain() {
    const width = parseFloat(document.getElementById("widthMain").value) || 0;
    const height = parseFloat(document.getElementById("heightMain").value) || 0;
    const colorDepth = parseFloat(document.getElementById("colorDepthMain").value) || 8;
    const framerate = parseFloat(document.getElementById("framerateMain").value) || 30;
    const chroma = document.getElementById("chromaMain").value;

    let baseBitrate = parseFloat(document.getElementById("bitrateMain").value) || 0; // in Mbps
    const audioKbps = parseFloat(document.getElementById("audioBitrateMain").value) || 0;
    const codec = document.getElementById("codecMain").value;
    const isLog = document.getElementById("logMain").checked;
    const isHDR = document.getElementById("hdrMain").checked;
    const isAllIntra = document.getElementById("allIntraMain").checked;
    const durationMinutes = parseFloat(document.getElementById("durationMain").value) || 0;
    const durationSec = durationMinutes * 60;

    // Reference "baseline" for partial exponent
    const refWidth = 1920;
    const refHeight = 1080;
    const refDepth = 8;
    const refFramerate = 30;
    const refChromaFactor = 1; // for 4:2:0

    // We'll scale the user's "baseBitrate" by the ratio, but using partial exponent
    const exponent = 0.5; // square-root approach

    const wFactor = (width * height) / (refWidth * refHeight);
    const dFactor = colorDepth / refDepth;
    const fFactor = framerate / refFramerate;
    const cFactor = getChromaFactor(chroma) / refChromaFactor;

    // partial exponent means we take the product of these ratios, then raise to exponent
    const ratio = Math.pow(wFactor * dFactor * fFactor * cFactor, exponent);

    let finalVideoBitrate = baseBitrate * ratio;

    // Now apply codec, LOG, HDR, All-Intra multipliers
    if (codec === "H.265") {
        finalVideoBitrate *= 0.6;
    }
    if (isLog) {
        finalVideoBitrate *= 1.1;
    }
    if (isHDR) {
        finalVideoBitrate *= 1.1;
    }
    if (isAllIntra) {
        finalVideoBitrate *= 2.0;
    }

    // Add audio
    const audioMbps = audioKbps / 1000;
    const totalMbps = finalVideoBitrate + audioMbps;

    // Convert to size
    const totalBits = totalMbps * 1e6 * durationSec;
    const totalBytes = totalBits / 8;
    const totalMB = totalBytes / 1e6;
    const totalGB = totalMB / 1000;

    return {totalMB, totalGB};
}

/* Same logic for the Proxy tab. */
function calculateProxy() {
    const width = parseFloat(document.getElementById("widthProxy").value) || 0;
    const height = parseFloat(document.getElementById("heightProxy").value) || 0;
    const colorDepth = parseFloat(document.getElementById("colorDepthProxy").value) || 8;
    const framerate = parseFloat(document.getElementById("framerateProxy").value) || 30;
    const chroma = document.getElementById("chromaProxy").value;

    let baseBitrate = parseFloat(document.getElementById("bitrateProxy").value) || 0; // in Mbps
    const audioKbps = parseFloat(document.getElementById("audioBitrateProxy").value) || 0;
    const codec = document.getElementById("codecProxy").value;
    const isLog = document.getElementById("logProxy").checked;
    const isHDR = document.getElementById("hdrProxy").checked;
    const isAllIntra = document.getElementById("allIntraProxy").checked;

    // Duration is synced with main
    const durationMinutes = parseFloat(document.getElementById("durationMain").value) || 0;
    const durationSec = durationMinutes * 60;

    // Partial exponent approach
    const refWidth = 1920;
    const refHeight = 1080;
    const refDepth = 8;
    const refFramerate = 30;
    const refChromaFactor = 1;

    const exponent = 0.5; // square-root scaling

    const wFactor = (width * height) / (refWidth * refHeight);
    const dFactor = colorDepth / refDepth;
    const fFactor = framerate / refFramerate;
    const cFactor = getChromaFactor(chroma) / refChromaFactor;

    const ratio = Math.pow(wFactor * dFactor * fFactor * cFactor, exponent);

    let finalVideoBitrate = baseBitrate * ratio;

    // Codec, log, HDR, All-Intra
    if (codec === "H.265") {
        finalVideoBitrate *= 0.6;
    }
    if (isLog) {
        finalVideoBitrate *= 1.1;
    }
    if (isHDR) {
        finalVideoBitrate *= 1.1;
    }
    if (isAllIntra) {
        finalVideoBitrate *= 2.0;
    }

    // Add audio
    const audioMbps = audioKbps / 1000;
    const totalMbps = finalVideoBitrate + audioMbps;

    // Convert to size
    const totalBits = totalMbps * 1e6 * durationSec;
    const totalBytes = totalBits / 8;
    const totalMB = totalBytes / 1e6;
    const totalGB = totalMB / 1000;

    return {totalMB, totalGB};
}

/* formatSize() nicely formats MB/GB/TB. */
function formatSize(MB, GB) {
    const MBtxt = MB.toFixed(2) + " MB";
    let secondPart = GB.toFixed(2) + " GB";
    if (GB >= 1000) {
        const TB = GB / 1000;
        secondPart = TB.toFixed(2) + " TB";
    }
    return `${MBtxt} (${secondPart})`;
}

/* updateSize() calculates both Main and Proxy and updates the UI. */
function updateSize() {
    const {totalMB: mainMB, totalGB: mainGB} = calculateMain();

    let proxyMB = 0, proxyGB = 0;
    if (proxyEnabled) {
        const {totalMB, totalGB} = calculateProxy();
        proxyMB = totalMB;
        proxyGB = totalGB;
    }

    document.getElementById("resultMain").innerText = `~ Main Storage: ` + formatSize(mainMB, mainGB);

    document.getElementById("resultProxy").innerText = `~ Proxy Storage: ` + formatSize(proxyMB, proxyGB);

    const totalMB = mainMB + proxyMB;
    const totalGB = mainGB + proxyGB;
    document.getElementById("resultTotal").innerText = `~ Total: ` + formatSize(totalMB, totalGB);
}

/* resetForm() sets everything back to defaults and disables proxy. */
function resetForm() {
    // Main defaults
    document.getElementById("widthMain").value = 3840;
    document.getElementById("heightMain").value = 2160;
    document.getElementById("bitrateMain").value = 200;
    document.getElementById("audioBitrateMain").value = 192;
    document.getElementById("colorDepthMain").value = 10;
    document.getElementById("framerateMain").value = 60;
    document.getElementById("chromaMain").value = "4:2:2";
    document.getElementById("codecMain").value = "H.264";
    document.getElementById("logMain").checked = false;
    document.getElementById("hdrMain").checked = false;
    document.getElementById("allIntraMain").checked = false;
    document.getElementById("durationMain").value = 60;

    // Proxy defaults
    document.getElementById("widthProxy").value = 1280;
    document.getElementById("heightProxy").value = 720;
    document.getElementById("bitrateProxy").value = 6;
    document.getElementById("audioBitrateProxy").value = 128;
    document.getElementById("colorDepthProxy").value = 8;
    document.getElementById("framerateProxy").value = 30;
    document.getElementById("chromaProxy").value = "4:2:0";
    document.getElementById("codecProxy").value = "H.264";
    document.getElementById("logProxy").checked = false;
    document.getElementById("hdrProxy").checked = false;
    document.getElementById("allIntraProxy").checked = false;
    document.getElementById("durationProxy").value = 60;

    // Turn proxy off
    proxyEnabled = false;
    document.getElementById("proxyTabBtn").style.display = "none";
    document.getElementById("toggleProxyBtn").textContent = "Enable Proxy";
    openTab("main");
    updateSize();
}