<footer id="myFooter" class="bg-secondary">
    <div class="container bg-secondary">
        <p class="px-5">Thank you for visiting our website! The Virtual Honolulu Control Facility ARTCC (vHCF) is a community of flight simulation enthusiasts providing air traffic control services on the VATSIM network. We take pride in fostering an immersive and enjoyable environment for controllers and pilots. While we occupy the same geographic area in our network coverage, we are not affiliated with the real-world HCF ARTCC or FAA.</p>
		<p class="px-5">vHCF is an affiliate of the <a href="http://www.vatsim.net" target="_blank"><img src="/photos/vatsim_logo.png" class="vatsim_logo" alt="VATSIM"></a> network and the <a href="http://www.vatusa.net" target="_blank"><img src="/photos/vatusa_logo.png" class="vatusa_logo" alt="VATUSA"></a> division</p>
        <p><a href="/privacy" target="_blank">Privacy Policy, Terms and Conditions</a></p>
        <p class="footer-copyright text-dark">© {{ Carbon\Carbon::now()->year }} vHCF ARTCC</p>
        @if(Carbon\Carbon::now()->month == 12)
            <button class="btn btn-dark btn-sm" onclick="window.snowStorm.stop();return false">Stop Snow</button>
        @endif

    </div>
</footer>
