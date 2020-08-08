# Swoole Video Streaming System
Video Streaming System using `swoole` php extension as its server while socket.js and other related files as its clients.



## Usage
- clone this repo
- open terminal and type `php server.php`
- open localhost:8000/send.html to start live stream
- open localhost:8000/receive.html to receive live stream




## Notes


1. The navigator.getMedia function in socket.js is deprecated and should be fixed in order to fit the latest browser.

2. The network delay will be accumulated as the time passes by in both functions but while streaming the video from camrea, the delay will go lower as the compression percentage is set at lower level.

3. While using local video to broadcast, the delay in local area network is very high though the compression level is set to an extremely lowest level.


---

<p align="right">by Alexander Ezharjan</p>
<p align="right">on 8th August,2020</p>