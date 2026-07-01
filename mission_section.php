<!-- ══ OUR MISSION SECTION ══ -->
<style>
.mission-section {
    background: #f5a623;
    padding: 90px 20px;
}
.mission-wrap {
    max-width: 1080px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1.1fr;
    gap: 80px;
    align-items: center;
}
@media (max-width: 768px) {
    .mission-wrap { grid-template-columns: 1fr; gap: 40px; }
}

/* LEFT TEXT */
.mission-eyebrow {
    font-size: 10px; font-weight: 700; letter-spacing: 4px;
    text-transform: uppercase; color: #0a0a0a;
    opacity: 0.6; margin-bottom: 14px;
}
.mission-title {
    font-family: 'Montserrat', sans-serif;
    font-size: clamp(30px, 4vw, 46px); font-weight: 900;
    color: #fff; line-height: 1.1; margin-bottom: 20px;
    text-transform: none !important; letter-spacing: -1px;
}
.mission-desc {
    font-size: 15px; color: #fff;
    line-height: 1.85; margin-bottom: 32px;
    opacity: 0.85;
}

/* Blue Card */
.mission-blue-card {
    background: #0d1f3c;
    border-radius: 16px;
    padding: 28px 26px;
}
.mission-blue-card ul {
    list-style: none; padding: 0; margin: 0;
    display: flex; flex-direction: column; gap: 18px;
}
.mission-blue-card ul li {
    display: flex; align-items: flex-start; gap: 12px;
    padding-bottom: 18px;
    border-bottom: 1px solid rgba(255,255,255,0.07);
}
.mission-blue-card ul li:last-child { border-bottom: none; padding-bottom: 0; }
.li-icon { font-size: 18px; flex-shrink: 0; margin-top: 2px; }
.li-title {
    font-family: 'Montserrat', sans-serif;
    font-size: 14px; font-weight: 700; color: #fff;
    margin-bottom: 4px; text-transform: none !important;
}
.li-desc { font-size: 12px; color: rgba(255,255,255,0.5); line-height: 1.6; }

/* RIGHT Slideshow */
.ms-slider-wrap {
    position: relative; border-radius: 20px; overflow: hidden;
    box-shadow: 0 24px 60px rgba(0,0,0,0.4);
    height: 460px;
}
.ms-slide img { 
    width: 100%; 
    height: 100%; 
    object-fit: contain !important;
    background: #0d1f3c;
    display: block; 
}
.ms-slide { position: absolute; inset: 0; opacity: 0; transition: opacity 0.7s ease; }
.ms-slide.active { opacity: 1; }
.ms-slide img { width: 100%; height: 100%; object-fit: cover; display: block; }
.ms-slide-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.05) 60%);
}
.ms-slide-info { position: absolute; bottom: 0; left: 0; right: 0; padding: 22px 24px; }
.ms-slide-info h4 {
    font-family: 'Montserrat', sans-serif; font-size: 18px; font-weight: 800;
    color: #fff; margin-bottom: 4px; text-transform: none !important;
}
.ms-slide-info p { font-size: 12px; color: rgba(255,255,255,0.5); margin: 0; }
.ms-dots { position: absolute; bottom: 20px; right: 20px; display: flex; gap: 6px; z-index: 10; }
.ms-dot {
    width: 8px; height: 8px; border-radius: 50%;
    background: rgba(255,255,255,0.4); cursor: pointer;
    transition: all 0.3s; border: none; padding: 0;
}
.ms-dot.active { background: #fff; width: 22px; border-radius: 4px; }
</style>

<section class="mission-section" id="about">
    <div class="mission-wrap">

        <!-- LEFT -->
        <div>
            <p class="mission-eyebrow">Who We Are</p>
            <h2 class="mission-title">Our Mission</h2>
            <p class="mission-desc">
                India is vast, diverse, and endlessly beautiful — but planning a trip can be overwhelming.
                Explore India brings everything you need for the perfect journey into one seamless platform.
            </p>

            <div class="mission-blue-card">
                <ul>
                    <li>
                        <span class="li-icon">🗺️</span>
                        <div>
                            <div class="li-title">Special &amp; Custom Packages</div>
                            <div class="li-desc">Curated North, South, East &amp; West packages — or build your own by picking cities, hotels and days your way.</div>
                        </div>
                    </li>
                    <li>
                        <span class="li-icon">👤</span>
                        <div>
                            <div class="li-title">Local Guide Support</div>
                            <div class="li-desc">Every trip comes with a verified local guide who knows the region inside out.</div>
                        </div>
                    </li>
                    <li>
                        <span class="li-icon">✈️</span>
                        <div>
                            <div class="li-title">Flights · Trains · Cabs</div>
                            <div class="li-desc">Book flights, find trains with PNR tracking, or hire an intercity cab — all in one place.</div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- RIGHT: Slideshow -->
        <div class="ms-slider-wrap">
            <div class="ms-slide active">
                <img src="images/300SM1103747.jpg" alt="North India">
                <div class="ms-slide-overlay"></div>
              
            </div>
            <div class="ms-slide">
                <img src="images/399SM1109860.jpg" alt="South India">
                <div class="ms-slide-overlay"></div>
               
            </div>
            <div class="ms-slide">
                <img src="images/9SM384509.jpg" alt="East India">
                <div class="ms-slide-overlay"></div>
               
            </div>
            <div class="ms-slide">
                <img src="images/220SM669076.jpg" alt="West India">
                <div class="ms-slide-overlay"></div>
              
            </div>
            <div class="ms-slide">
                <img src="images/220SM868490.jpg" alt="Taj Mahal">
                <div class="ms-slide-overlay"></div>
              
            </div>
            <div class="ms-dots">
                <button class="ms-dot active" onclick="goMS(0)"></button>
                <button class="ms-dot" onclick="goMS(1)"></button>
                <button class="ms-dot" onclick="goMS(2)"></button>
                <button class="ms-dot" onclick="goMS(3)"></button>
                <button class="ms-dot" onclick="goMS(4)"></button>
            </div>
        </div>

    </div>
</section>

<script>
(function(){
    var slides  = document.querySelectorAll('.ms-slide');
    var dots    = document.querySelectorAll('.ms-dot');
    var current = 0;
    function goMS(n) {
        slides[current].classList.remove('active');
        dots[current].classList.remove('active');
        current = n;
        slides[current].classList.add('active');
        dots[current].classList.add('active');
    }
    window.goMS = goMS;
    setInterval(function(){ goMS((current + 1) % slides.length); }, 3500);
})();
</script>