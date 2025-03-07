<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Video Storage Calculator</title>
    <!-- Tailwind CSS -->
    <script src="./tailwindcss.js"></script>
    <meta name="author" content="MisterBR"/>
</head>
<body class="bg-gray-100 min-h-screen p-4" onload="initializePage()">
<div class="max-w-7xl mx-auto">
    <!-- Page Title -->
    <h1 class="text-center text-2xl font-bold mb-6">
        Video Storage Calculator
    </h1>

    <!-- Tabs Navigation -->
    <div class="flex border-b mb-4">
        <!-- Main Tab Button: Pre-styled as active by default -->
        <button id="mainTabBtn"
                class="px-4 py-2 border-b-2 bg-blue-100 text-blue-600 border-blue-600 focus:outline-none"
                onclick="openTab('main')">
            Main Recording
        </button>

        <!-- Proxy Tab Button: hidden when proxy is disabled -->
        <button id="proxyTabBtn" class="px-4 py-2 border-b-2 bg-gray-200 focus:outline-none hidden"
                onclick="openTab('proxy')">
            Proxy Recording
        </button>
    </div>

    <div class="flex flex-col md:flex-row gap-6">
        <!-- Left Column: Main / Proxy Form -->
        <div class="w-full md:w-1/2">
            <!-- Main Recording Form -->
            <div id="mainTab" style="display: block;">
                <h2 class="text-lg font-semibold mb-4">Main Recording</h2>

                <!-- 2-column grid for compact layout -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- Width -->
                    <div>
                        <label class="block mb-1" for="widthMain">Width:</label>
                        <input type="number" id="widthMain" name="widthMain" class="w-full p-2 border rounded-lg mb-3"
                               min="1" value="3840" oninput="updateSize()"/>
                    </div>

                    <!-- Height -->
                    <div>
                        <label class="block mb-1" for="heightMain">Height:</label>
                        <input type="number" id="heightMain" name="heightMain" class="w-full p-2 border rounded-lg mb-3"
                               min="1" value="2160" oninput="updateSize()"/>
                    </div>

                    <!-- Video Bitrate (Mbps) -->
                    <div>
                        <label class="block mb-1" for="bitrateMain">Video Bitrate (Mbps):</label>
                        <input type="number" id="bitrateMain" name="bitrateMain"
                               class="w-full p-2 border rounded-lg mb-3" min="1" value="200" oninput="updateSize()"/>
                    </div>

                    <!-- Audio Bitrate (kbps) -->
                    <div>
                        <label class="block mb-1" for="audioBitrateMain">Audio Bitrate (kbps):</label>
                        <input type="number" id="audioBitrateMain" name="audioBitrateMain"
                               class="w-full p-2 border rounded-lg mb-3" min="1" value="192" oninput="updateSize()"/>
                    </div>

                    <!-- Color Depth (bits) -->
                    <div>
                        <label class="block mb-1" for="colorDepthMain">Color Depth (bits):</label>
                        <input type="number" id="colorDepthMain" name="colorDepthMain"
                               class="w-full p-2 border rounded-lg mb-3" min="1" value="10" oninput="updateSize()"/>
                    </div>

                    <!-- Frame Rate (fps) -->
                    <div>
                        <label class="block mb-1" for="framerateMain">Frame Rate (fps):</label>
                        <input type="number" id="framerateMain" name="framerateMain"
                               class="w-full p-2 border rounded-lg mb-3" min="1" value="60" oninput="updateSize()"/>
                    </div>

                    <!-- Chroma -->
                    <div>
                        <label class="block mb-1" for="chromaMain">Chroma Subsampling:</label>
                        <select id="chromaMain" name="chromaMain" class="w-full p-2 border rounded-lg mb-3"
                                onchange="updateSize()">
                            <option value="4:1:1">4:1:1</option>
                            <option value="4:2:0">4:2:0</option>
                            <option value="4:2:2" selected>4:2:2</option>
                            <option value="4:4:2">4:4:2</option>
                            <option value="4:4:4">4:4:4</option>
                        </select>
                    </div>

                    <!-- Video Codec -->
                    <div>
                        <label class="block mb-1" for="codecMain">Video Codec:</label>
                        <select id="codecMain" name="codecMain" class="w-full p-2 border rounded-lg mb-3"
                                onchange="updateSize()">
                            <option value="H.264">H.264</option>
                            <option value="H.265">H.265</option>
                        </select>
                    </div>

                    <!-- Checkboxes (LOG, HDR, All-Intra) -->
                    <div class="col-span-2">
                        <div class="mb-3">
                            <label class="inline-flex items-center mr-4">
                                <input type="checkbox" id="logMain" class="mr-1" onchange="updateSize()"/>
                                LOG
                            </label>
                            <label class="inline-flex items-center mr-4">
                                <input type="checkbox" id="hdrMain" class="mr-1" onchange="updateSize()"/>
                                HDR
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" id="allIntraMain" class="mr-1" onchange="updateSize()"/>
                                All-Intra
                            </label>
                        </div>
                    </div>

                    <!-- Duration (minutes) -->
                    <div class="col-span-2">
                        <label class="block mb-1" for="durationMain">Duration (minutes):</label>
                        <input type="number" id="durationMain" name="durationMain"
                               class="w-full p-2 border rounded-lg mb-3" min="1" value="60"
                               oninput="syncDurationFromMain()"/>
                    </div>
                </div>
            </div>

            <!-- Proxy Recording Form -->
            <div id="proxyTab" style="display: none;">
                <h2 class="text-lg font-semibold mb-4">Proxy Recording</h2>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Width -->
                    <div>
                        <label class="block mb-1" for="widthProxy">Width:</label>
                        <input type="number" id="widthProxy" name="widthProxy" class="w-full p-2 border rounded-lg mb-3"
                               min="1" value="1280" oninput="updateSize()"/>
                    </div>

                    <!-- Height -->
                    <div>
                        <label class="block mb-1" for="heightProxy">Height:</label>
                        <input type="number" id="heightProxy" name="heightProxy"
                               class="w-full p-2 border rounded-lg mb-3" min="1" value="720" oninput="updateSize()"/>
                    </div>

                    <!-- Video Bitrate (Mbps) -->
                    <div>
                        <label class="block mb-1" for="bitrateProxy">Video Bitrate (Mbps):</label>
                        <input type="number" id="bitrateProxy" name="bitrateProxy"
                               class="w-full p-2 border rounded-lg mb-3" min="1" value="6" oninput="updateSize()"/>
                    </div>

                    <!-- Audio Bitrate (kbps) -->
                    <div>
                        <label class="block mb-1" for="audioBitrateProxy">Audio Bitrate (kbps):</label>
                        <input type="number" id="audioBitrateProxy" name="audioBitrateProxy"
                               class="w-full p-2 border rounded-lg mb-3" min="1" value="128" oninput="updateSize()"/>
                    </div>

                    <!-- Color Depth (bits) -->
                    <div>
                        <label class="block mb-1" for="colorDepthProxy">Color Depth (bits):</label>
                        <input type="number" id="colorDepthProxy" name="colorDepthProxy"
                               class="w-full p-2 border rounded-lg mb-3" min="1" value="8" oninput="updateSize()"/>
                    </div>

                    <!-- Frame Rate (fps) -->
                    <div>
                        <label class="block mb-1" for="framerateProxy">Frame Rate (fps):</label>
                        <input type="number" id="framerateProxy" name="framerateProxy"
                               class="w-full p-2 border rounded-lg mb-3" min="1" value="30" oninput="updateSize()"/>
                    </div>

                    <!-- Chroma -->
                    <div>
                        <label class="block mb-1" for="chromaProxy">Chroma Subsampling:</label>
                        <select id="chromaProxy" name="chromaProxy" class="w-full p-2 border rounded-lg mb-3"
                                onchange="updateSize()">
                            <option value="4:1:1">4:1:1</option>
                            <option value="4:2:0" selected>4:2:0</option>
                            <option value="4:2:2">4:2:2</option>
                            <option value="4:4:2">4:4:2</option>
                            <option value="4:4:4">4:4:4</option>
                        </select>
                    </div>

                    <!-- Video Codec -->
                    <div>
                        <label class="block mb-1" for="codecProxy">Video Codec:</label>
                        <select id="codecProxy" name="codecProxy" class="w-full p-2 border rounded-lg mb-3"
                                onchange="updateSize()">
                            <option value="H.264">H.264</option>
                            <option value="H.265">H.265</option>
                        </select>
                    </div>

                    <!-- Checkboxes (LOG, HDR, All-Intra) -->
                    <div class="col-span-2">
                        <div class="mb-3">
                            <label class="inline-flex items-center mr-4">
                                <input type="checkbox" id="logProxy" class="mr-1" onchange="updateSize()"/>
                                LOG
                            </label>
                            <label class="inline-flex items-center mr-4">
                                <input type="checkbox" id="hdrProxy" class="mr-1" onchange="updateSize()"/>
                                HDR
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" id="allIntraProxy" class="mr-1" onchange="updateSize()"/>
                                All-Intra
                            </label>
                        </div>
                    </div>

                    <!-- Duration (minutes) -->
                    <div class="col-span-2">
                        <label class="block mb-1" for="durationProxy">Duration (minutes):</label>
                        <input type="number" id="durationProxy" name="durationProxy"
                               class="w-full p-2 border rounded-lg mb-3" min="1" value="60"
                               oninput="syncDurationFromProxy()"/>
                    </div>
                </div>
            </div>

            <!-- Buttons: Reset + Enable/Disable Proxy -->
            <div class="flex gap-2 mt-4">
                <button type="button" class="bg-gray-300 text-black px-4 py-2 rounded-lg hover:bg-gray-400"
                        onclick="resetForm()">
                    Reset
                </button>
                <button type="button" id="toggleProxyBtn"
                        class="bg-orange-400 text-black px-10 py-2 rounded-lg hover:bg-orange-500"
                        onclick="toggleProxy()">
                    Enable Proxy
                </button>
            </div>
        </div>

        <!-- Right Column: Resolutions Tables & Results -->
        <div class="w-full md:w-1/2">
            <!-- Standard Resolutions Table -->
            <h3 class="text-lg font-semibold mb-2">Standard Resolutions</h3>
            <table class="w-full border-collapse border border-gray-300 text-center mb-6">
                <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">Resolution</th>
                    <th class="border p-2">Normal</th>
                    <th class="border p-2">Aspect Ratio</th>
                </tr>
                </thead>
                <tbody>
                <tr class="cursor-pointer" onclick="setResolution(640, 360)">
                    <td class="border p-2">360p</td>
                    <td class="border p-2">640x360</td>
                    <td class="border p-2">16:9</td>
                </tr>
                <tr class="cursor-pointer" onclick="setResolution(854, 480)">
                    <td class="border p-2">480p</td>
                    <td class="border p-2">854x480</td>
                    <td class="border p-2">16:9</td>
                </tr>
                <tr class="cursor-pointer" onclick="setResolution(1280, 720)">
                    <td class="border p-2">720p</td>
                    <td class="border p-2">1280x720</td>
                    <td class="border p-2">16:9</td>
                </tr>
                <tr class="cursor-pointer" onclick="setResolution(1920, 1080)">
                    <td class="border p-2">FHD</td>
                    <td class="border p-2">1920x1080</td>
                    <td class="border p-2">16:9</td>
                </tr>
                <tr class="cursor-pointer" onclick="setResolution(2048, 1080)">
                    <td class="border p-2">2K</td>
                    <td class="border p-2">2048x1080</td>
                    <td class="border p-2">17:9</td>
                </tr>
                <tr class="cursor-pointer" onclick="setResolution(3840, 2160)">
                    <td class="border p-2">4K</td>
                    <td class="border p-2">3840x2160</td>
                    <td class="border p-2">16:9</td>
                </tr>
                <tr class="cursor-pointer" onclick="setResolution(6144, 3160)">
                    <td class="border p-2">6K</td>
                    <td class="border p-2">6144x3160</td>
                    <td class="border p-2">19:10</td>
                </tr>
                <tr class="cursor-pointer" onclick="setResolution(7680, 4320)">
                    <td class="border p-2">8K</td>
                    <td class="border p-2">7680x4320</td>
                    <td class="border p-2">16:9</td>
                </tr>
                <tr class="cursor-pointer" onclick="setResolution(15360, 8640)">
                    <td class="border p-2">16K</td>
                    <td class="border p-2">15360x8640</td>
                    <td class="border p-2">16:9</td>
                </tr>
                </tbody>
            </table>

            <!-- Results Display (with green background) -->
            <div class="p-4 bg-green-100 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-2">Results</h3>
                <p id="resultMain" class="mb-2 text-sm"></p>
                <p id="resultProxy" class="mb-2 text-sm"></p>
                <hr class="my-2"/>
                <p id="resultTotal" class="font-bold"></p>
            </div>
        </div>
    </div>
</div>
<script src="./scripts.js?v=1.0.0"></script>
</body>
</html>