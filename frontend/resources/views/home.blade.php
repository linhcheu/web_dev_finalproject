@extends('layouts.app')

@section('title', 'CareConnect - Connecting Users with Hospitals')

@section('content')
<!-- Hero Section -->
<section class="hero section-blue section-texture" style="background:linear-gradient(135deg, var(--primary-color), var(--secondary-color)), url('https://www.transparenttextures.com/patterns/cubes.png');background-blend-mode:lighten;padding:3rem 0 2rem 0;" data-aos="fade-up" data-aos-duration="900">
    <div class="container" style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:2rem;">
        <div class="hero-content" style="flex:1;min-width:260px;">
            <h1 style="color:white;font-size:2.5rem;font-weight:700;margin-bottom:1rem;"><i class="fas fa-stethoscope"></i> Connecting You with Quality Healthcare</h1>
            <p style="color:white;font-size:1.2rem;margin-bottom:2rem;">Find and book appointments with trusted hospitals in your area. CareConnect makes healthcare accessible, secure, and convenient.</p>
            <div class="hero-buttons" style="display:flex;gap:1.2rem;">
                <a href="{{ route('hospitals.index') }}" class="btn btn-primary" style="border-radius:2rem;padding:0.8rem 2.2rem;font-size:1.1rem;"><i class="fas fa-hospital-alt"></i> Find Hospitals</a>
                <a href="{{ route('register') }}" class="btn btn-outline" style="color:white;border-radius:2rem;padding:0.8rem 2.2rem;font-size:1.1rem;border:2px solid #fff;"><i class="fas fa-user-plus"></i> Get Started</a>
            </div>
        </div>
        <div class="hero-image" style="flex:1;min-width:220px;text-align:center;">
            <i class="fas fa-hospital-user" style="font-size:6rem;color:white;"></i>
        </div>
    </div>
</section>

<!-- What is CareConnect Section -->
<section class="section section-white" style="background:rgba(255,255,255,0.85);backdrop-filter:blur(8px);padding:3rem 0;" data-aos="fade-up" data-aos-delay="200" data-aos-duration="900">
    <div class="container">
        <div class="section-header text-center" style="background:white;backdrop-filter:blur(8px);padding:2rem;border-radius:0.75rem;box-shadow:0 4px 24px 0 rgba(0,0,0,0.08),0 1.5px 4px 0 rgba(0,188,212,0.07);margin-bottom:2rem;border:1.5px solid #e0e7ef;position:relative;overflow:hidden;">
            <h2 style="color:var(--primary-color);font-size:2rem;"><i class="fas fa-question-circle"></i> What is CareConnect?</h2>
            <p>Your trusted platform for connecting with healthcare providers</p>
        </div>
        <div class="grid grid-3" style="display:flex;gap:2rem;flex-wrap:wrap;justify-content:center;">
            <div class="feature-card" style="background:rgba(255,255,255,0.95);border-radius:1.2rem;box-shadow:0 4px 24px 0 rgba(0,0,0,0.08);padding:2rem 1.5rem;text-align:center;flex:1 1 220px;max-width:340px;">
                <div class="feature-icon" style="font-size:2.2rem;color:var(--primary-color);margin-bottom:0.7rem;"><i class="fas fa-search"></i></div>
                <h3>Find Hospitals</h3>
                <p>Discover certified hospitals and medical facilities in your area with detailed information and reviews.</p>
            </div>
            <div class="feature-card" style="background:rgba(255,255,255,0.95);border-radius:1.2rem;box-shadow:0 4px 24px 0 rgba(0,0,0,0.08);padding:2rem 1.5rem;text-align:center;flex:1 1 220px;max-width:340px;">
                <div class="feature-icon" style="font-size:2.2rem;color:var(--primary-color);margin-bottom:0.7rem;"><i class="fas fa-calendar-check"></i></div>
                <h3>Book Appointments</h3>
                <p>Schedule appointments with ease through our secure booking system. No more waiting on hold.</p>
            </div>
            <div class="feature-card" style="background:rgba(255,255,255,0.95);border-radius:1.2rem;box-shadow:0 4px 24px 0 rgba(0,0,0,0.08);padding:2rem 1.5rem;text-align:center;flex:1 1 220px;max-width:340px;">
                <div class="feature-icon" style="font-size:2.2rem;color:var(--primary-color);margin-bottom:0.7rem;"><i class="fas fa-shield-alt"></i></div>
                <h3>Secure & Private</h3>
                <p>Your health information is protected with industry-standard security measures and privacy controls.</p>
            </div>
        </div>
    </div>
</section>

<!-- Why Trust Us Section -->
<section class="section bg-light"style="background:var(--primary-color); color:black; backdrop-filter:blur(8px);padding:3rem 0; data-aos="fade-up" data-aos-delay="200" data-aos-duration="900" >
    <div class="container" style="background:white;backdrop-filter:blur(8px);padding:2rem;border-radius:0.75rem;box-shadow:0 4px 24px 0 rgba(0,0,0,0.08),0 1.5px 4px 0 rgba(0,188,212,0.07);margin-bottom:2rem;border:1.5px solid #e0e7ef;position:relative;overflow:hidden;">
        <div class="section-header text-center" style="background:white;backdrop-filter:blur(8px);padding:2rem;border-radius:0.75rem;box-shadow:0 4px 24px 0 rgba(0,0,0,0.08),0 1.5px 4px 0 rgba(0,188,212,0.07);margin-bottom:2rem;border:1.5px solid #e0e7ef;position:relative;overflow:hidden;">
            <h2>Why You Should Trust Us</h2>
            <p>We prioritize your health and security above everything else</p>
        </div>
        <div class="grid grid-2">
            <div class="trust-content">
                <div class="trust-item">
                    <i class="fas fa-certificate"></i>
                    <div>
                        <h4>Certified Hospitals</h4>
                        <p>All hospitals on our platform are verified and certified by healthcare authorities.</p>
                    </div>
                </div>
                <div class="trust-item">
                    <i class="fas fa-lock"></i>
                    <div>
                        <h4>Secure Platform</h4>
                        <p>Your data is encrypted and protected with the highest security standards.</p>
                    </div>
                </div>
                <div class="trust-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <h4>Quick Access</h4>
                        <p>Get instant access to healthcare services without long waiting times.</p>
                    </div>
                </div>
            </div>
            <div class="trust-image">
                <i class="fas fa-user-shield"></i>
            </div>
        </div>
    </div>
</section>

<!-- Our Team Section -->
    <section class="section" data-aos="fade-up" data-aos-delay="200" data-aos-duration="900">
    <div class="container" style="background:white;backdrop-filter:blur(8px);padding:2rem;border-radius:0.75rem;box-shadow:0 4px 24px 0 rgba(0,0,0,0.08),0 1.5px 4px 0 rgba(0,188,212,0.07);margin-bottom:2rem;border:1.5px solid #e0e7ef;position:relative;overflow:hidden;">
        <div class="section-header text-center" style="background:white;backdrop-filter:blur(8px);padding:2rem;border-radius:0.75rem;box-shadow:0 4px 24px 0 rgba(0,0,0,0.08),0 1.5px 4px 0 rgba(0,188,212,0.07);margin-bottom:2rem;border:1.5px solid #e0e7ef;position:relative;overflow:hidden;">
            <h2>Our Team</h2>
            <p>Meet the professionals behind CareConnect</p>
        </div>
        <div class="grid grid-3">
            <div class="team-card">
                <div class="team-avatar">
                    <img src="{{ asset('images/team/sothearith.png') }}" alt="Sothearith Horn" class="team-image">
                </div>
                <h4>Sothearith Horn</h4>
                <p class="team-role">Backend</p>
                <p class="team-secondary-role">COO</p>
                <p>Leading our backend development with expertise in scalable architecture and system optimization.</p>
            </div>
            <div class="team-card">
                <div class="team-avatar">
                    <img src="{{ asset('images/team/linhcheu.png') }}" alt="Linhcheu Meng" class="team-image">
                </div>
                <h4>Linhcheu Meng</h4>
                <p class="team-role">API</p>
                <p class="team-secondary-role">CEO</p>
                <p>Driving our API development and leading the company's strategic vision and growth.</p>
            </div>
            <div class="team-card">
                <div class="team-avatar">
                    <img src="{{ asset('images/team/kimlong.png') }}" alt="Kimlong Neng" class="team-image">
                </div>
                <h4>Kimlong Neng</h4>
                <p class="team-role">Frontend</p>
                <p class="team-secondary-role">CFO</p>
                <p>Creating exceptional user experiences through frontend development and managing financial operations.</p>
            </div>
        </div>
    </div>
</section>

<!-- Popular Hospitals Section -->
<section class="section bg-light" style="background:var(--light-color); color:black; backdrop-filter:blur(8px);padding:3rem 0; data-aos="fade-up" data-aos-delay="200" data-aos-duration="900"" >
    <div class="container" style="background:white;backdrop-filter:blur(8px);padding:2rem;border-radius:0.75rem;box-shadow:0 4px 24px 0 rgba(0,0,0,0.08),0 1.5px 4px 0 rgba(0,188,212,0.07);margin-bottom:2rem;border:1.5px solid #e0e7ef;position:relative;overflow:hidden;">
        <div class="section-header text-center" style="background:white;backdrop-filter:blur(8px);padding:2rem;border-radius:0.75rem;box-shadow:0 4px 24px 0 rgba(0,0,0,0.08),0 1.5px 4px 0 rgba(0,188,212,0.07);margin-bottom:2rem;border:1.5px solid #e0e7ef;position:relative;overflow:hidden;">
            <h2>Popular Hospitals</h2>
            <p>Top-rated healthcare facilities on our platform</p>
        </div>
        @if($popularHospitals->count() > 0)
            <div class="grid grid-3">
                @foreach($popularHospitals as $hospital)
                    <div class="hospital-card">
                        <div class="hospital-image">
                            @if($hospital->profile_picture)
                                <img src="{{ asset('storage/' . $hospital->profile_picture) }}" alt="{{ $hospital->name }}">
                            @else
                                <i class="fas fa-hospital"></i>
                            @endif
                        </div>
                        <div class="hospital-content">
                            <h4 class="hospital-name">{{ $hospital->name }}</h4>
                            <p class="hospital-location" style="color:black;"><i class="fas fa-map-marker-alt"></i> {{ $hospital->location }}</p>
                            <p class="hospital-description" style="color:black;">{{ Str::limit($hospital->information, 100) }}</p>
                            <p class="hospital-description" style="font-weight: bold; color:black;"> <i class="fas fa-map"></i> {{ $hospital->province }}</p>
                          
                            <a href="{{ route('hospitals.show', $hospital) }}" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state text-center">
                <div class="empty-icon">
                    <i class="fas fa-hospital"></i>
                </div>
                <h3>No Hospitals Available</h3>
                <p>There are currently no active hospitals in our system. Please check back later.</p>
            </div>
        @endif
    </div>
</section>

<!-- Our Partners Section -->
<section class="section" data-aos="fade-up" data-aos-delay="200" data-aos-duration="900">
    <div class="container" style="background:white;backdrop-filter:blur(8px);padding:2rem;border-radius:0.75rem;box-shadow:0 4px 24px 0 rgba(0,0,0,0.08),0 1.5px 4px 0 rgba(0,188,212,0.07);margin-bottom:2rem;border:1.5px solid #e0e7ef;position:relative;overflow:hidden;">
        <div class="section-header text-center" style="background:white;backdrop-filter:blur(8px);padding:2rem;border-radius:0.75rem;box-shadow:0 4px 24px 0 rgba(0,0,0,0.08),0 1.5px 4px 0 rgba(0,188,212,0.07);margin-bottom:2rem;border:1.5px solid #e0e7ef;position:relative;overflow:hidden;">
            <h2>Our Partner Hospitals</h2>
            <p>Trusted healthcare facilities on our platform</p>
        </div>
        @if($popularHospitals->count() > 0)
            <div class="partners-marquee-container" style="overflow:hidden;position:relative;background:linear-gradient(90deg, rgba(255,255,255,0.9), rgba(255,255,255,0.7), rgba(255,255,255,0.9));border-radius:1rem;padding:2rem 0;">
                <div class="partners-marquee" style="display:flex;animation:marqueeContinuous 25s linear infinite;gap:3rem;align-items:center;">
                    <!-- First set of logos -->
                    @foreach($popularHospitals as $hospital)
                        <div class="partner-logo-marquee" style="flex-shrink:0;display:flex;align-items:center;justify-content:center;width:80px;height:80px;background:white;border-radius:1rem;box-shadow:0 4px 15px rgba(0,0,0,0.1);margin:0 1rem;transition:all 0.3s ease;">
                            @if($hospital->profile_picture)
                                <img src="{{ asset('storage/' . $hospital->profile_picture) }}" alt="{{ $hospital->name }}" style="max-width: 50px; max-height: 50px; border-radius: 8px; object-fit: cover;">
                            @else
                                <i class="fas fa-hospital" style="font-size:2rem;color:var(--primary-color);"></i>
                            @endif
                        </div>
                    @endforeach
                    <!-- Duplicate set for seamless loop -->
                    @foreach($popularHospitals as $hospital)
                        <div class="partner-logo-marquee" style="flex-shrink:0;display:flex;align-items:center;justify-content:center;width:80px;height:80px;background:white;border-radius:1rem;box-shadow:0 4px 15px rgba(0,0,0,0.1);margin:0 1rem;transition:all 0.3s ease;">
                            @if($hospital->profile_picture)
                                <img src="{{ asset('storage/' . $hospital->profile_picture) }}" alt="{{ $hospital->name }}" style="max-width: 50px; max-height: 50px; border-radius: 8px; object-fit: cover;">
                            @else
                                <i class="fas fa-hospital" style="font-size:2rem;color:var(--primary-color);"></i>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="empty-state text-center">
                <div class="empty-icon">
                    <i class="fas fa-hospital"></i>
                </div>
                <h3>No Partner Hospitals</h3>
                <p>We're working to add more healthcare partners to our platform.</p>
            </div>
        @endif
    </div>
</section>

<!-- Customer Feedback Section -->
<section class="section bg-light" style="background:var(--light-color); color:black; backdrop-filter:blur(8px);padding:3rem 0;" data-aos="fade-up" data-aos-delay="200" data-aos-duration="900">
    <div class="container" style="background:white;backdrop-filter:blur(8px);padding:2rem;border-radius:0.75rem;box-shadow:0 4px 24px 0 rgba(0,0,0,0.08),0 1.5px 4px 0 rgba(0,188,212,0.07);margin-bottom:2rem;border:1.5px solid #e0e7ef;position:relative;overflow:hidden;">
        <div class="section-header text-center" style="background:white;backdrop-filter:blur(8px);padding:2rem;border-radius:0.75rem;box-shadow:0 4px 24px 0 rgba(0,0,0,0.08),0 1.5px 4px 0 rgba(0,188,212,0.07);margin-bottom:2rem;border:1.5px solid #e0e7ef;position:relative;overflow:hidden;">
            <h2>Customer Feedback</h2>
            <p>What our users say about CareConnect</p>
        </div>
        @if($recentFeedback->count() > 0)
            <div class="feedback-grid">
                @foreach($recentFeedback->take(10) as $index => $feedback)
                    <div class="feedback-card" data-aos="fade-up" data-aos-delay="{{ 100 + ($index * 50) }}">
                        <div class="feedback-content">
                            <p>"{{ Str::limit($feedback->comments, 150) }}"</p>
                        </div>
                        <div class="feedback-author">
                            <div class="author-avatar">
                                @if($feedback->user->profile_picture)
                                    <img src="{{ asset('storage/' . $feedback->user->profile_picture) }}" alt="User Avatar">
                                @else
                                    <div class="avatar-initials">
                                        {{ strtoupper(substr($feedback->user->first_name, 0, 1)) }}{{ strtoupper(substr($feedback->user->last_name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="author-info">
                                <h5>{{ $feedback->user->first_name }} {{ $feedback->user->last_name }}</h5>
                                <span>{{ ucfirst($feedback->user->role) }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state text-center">
                <div class="empty-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <h3>No Feedback Yet</h3>
                <p>Be the first to share your experience with CareConnect!</p>
            </div>
        @endif
    </div>
</section>

<!-- CTA Section -->
<section class="section cta-section">
    <div class="container">
        <div class="cta-content text-center" style="color: black;">
            <h2>Ready to Get Started?</h2>
            <p>Join thousands of users who trust CareConnect for their healthcare needs.</p>
            <div class="cta-buttons">
                <a href="{{ route('register') }}" class="btn btn-primary">Sign Up Now</a>
                <a href="{{ route('about') }}" class="btn btn-outline">Learn More</a>
            </div>
        </div>
    </div>
</section>

<!-- Back to Top Button -->
<button id="backToTopBtn" title="Back to top" class="back-to-top-btn" style="display:none;position:fixed;bottom:2.5rem;right:2.5rem;z-index:9999;background:var(--primary-color);color:#fff;border:none;border-radius:50%;width:52px;height:52px;box-shadow:0 4px 16px rgba(0,0,0,0.13);font-size:1.7rem;cursor:pointer;transition:opacity 0.4s,transform 0.4s;opacity:0;transform:translateY(30px);">
    <i class="fas fa-arrow-up"></i>
</button>
@endsection

@section('scripts')
<!-- AOS Animate On Scroll Library -->
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({
    once: true,
    duration: 900,
    offset: 80,
  });
  // Back to Top Button
  const backToTopBtn = document.getElementById('backToTopBtn');
  let backToTopVisible = false;
  window.addEventListener('scroll', function() {
    if (window.scrollY > 300) {
      if (!backToTopVisible) {
        backToTopBtn.style.display = 'block';
        setTimeout(() => {
          backToTopBtn.style.opacity = '1';
          backToTopBtn.style.transform = 'translateY(0)';
        }, 10);
        backToTopVisible = true;
      }
    } else {
      if (backToTopVisible) {
        backToTopBtn.style.opacity = '0';
        backToTopBtn.style.transform = 'translateY(30px)';
        setTimeout(() => {
          backToTopBtn.style.display = 'none';
        }, 400);
        backToTopVisible = false;
      }
    }
  });
  backToTopBtn.addEventListener('click', function() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
</script>
@endsection

@section('styles')
<!-- AOS Animate On Scroll CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" />
<style>
/* Hero Section */
.hero {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    padding: 4rem 0;
}

.hero .container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    align-items: center;
}

.hero-content h1 {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.hero-content p {
    font-size: 1.125rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.hero-buttons {
    display: flex;
    gap: 1rem;
}

.hero-image {
    text-align: center;
    font-size: 8rem;
    opacity: 0.8;
}

/* Section Styles */
.section {
    padding: 4rem 0;
}

.section-header {
    margin-bottom: 3rem;
}

.section-header h2 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.section-header p {
    font-size: 1.125rem;
    color: var(--gray-color);
}

.bg-light {
    background-color: var(--light-color);
}

/* Feature Cards */
.feature-card {
    text-align: center;
    padding: 2rem;
}

.feature-icon {
    font-size: 3rem;
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.feature-card h3 {
    margin-bottom: 1rem;
}

/* Trust Section */
.trust-content {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.trust-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.trust-item i {
    font-size: 2rem;
    color: var(--primary-color);
    margin-top: 0.5rem;
}

.trust-item h4 {
    margin-bottom: 0.5rem;
}

.trust-image {
    text-align: center;
    font-size: 6rem;
    color: var(--primary-color);
    opacity: 0.7;
}

/* Team Cards */
.team-card {
    text-align: center;
    padding: 2rem;
}

.team-avatar {
    width: 60px;
    height: 60px;
    background-color: var(--primary-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.5rem;
    color: white;
    overflow: hidden;
}

.team-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.team-role {
    color: var(--primary-color);
    font-weight: 500;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.team-secondary-role {
    color: var(--gray-color);
    font-size: 0.8rem;
    margin-bottom: 1rem;
    font-weight: 400;
}

/* Hospital Cards */
.hospital-card {
    background: white;
    border-radius: 0.5rem;
    box-shadow: var(--shadow);
    overflow: hidden;
    transition: transform 0.3s ease;
}

.hospital-card:hover {
    transform: translateY(-5px);
}

.hospital-image {
    height: 200px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    color: white;
}

.hospital-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.hospital-content {
    padding: 1.5rem;
}

.hospital-name {
    margin-bottom: 0.5rem;
    color: var(--dark-color);
}

.hospital-location {
    color: var(--gray-color);
    margin-bottom: 0.5rem;
}

.hospital-description {
    color: var(--gray-color);
    margin-bottom: 1rem;
    line-height: 1.5;
}

.subscription-badge {
    margin-bottom: 1rem;
}

.subscription-badge span {
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.plan-basic {
    background-color: #e3f2fd;
    color: #1976d2;
}

.plan-premium {
    background-color: #fff3e0;
    color: #f57c00;
}

.plan-enterprise {
    background-color: #f3e5f5;
    color: #7b1fa2;
}

/* Feedback Cards */
.feedback-card {
    background: white;
    padding: 2rem;
    border-radius: 0.5rem;
    box-shadow: var(--shadow);
    text-align: center;
}

.feedback-content p {
    font-style: italic;
    margin-bottom: 1.5rem;
    color: var(--gray-color);
    line-height: 1.6;
}

.feedback-author {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
}

.author-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}

.author-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.author-info h5 {
    margin: 0;
    color: var(--dark-color);
}

.author-info span {
    color: var(--gray-color);
    font-size: 0.875rem;
}

/* Partners Marquee */
.partners-marquee-container {
    overflow: hidden;
    position: relative;
    background: linear-gradient(90deg, rgba(255,255,255,0.9), rgba(255,255,255,0.7), rgba(255,255,255,0.9));
    border-radius: 1rem;
    padding: 2rem 0;
}

.partners-marquee {
    display: flex;
    animation: marquee 20s linear infinite;
    gap: 3rem;
    align-items: center;
}

.partner-logo-marquee {
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 80px;
    height: 80px;
    background: white;
    border-radius: 1rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    margin: 0 1rem;
    transition: all 0.3s ease;
}

.partner-logo-marquee:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.partner-logo-marquee img {
    max-width: 50px;
    max-height: 50px;
    border-radius: 8px;
    object-fit: cover;
}

.partner-logo-marquee i {
    font-size: 2rem;
    color: var(--primary-color);
}

@keyframes marqueeContinuous {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-50%);
    }
}

/* Pause animation on hover */
.partners-marquee-container:hover .partners-marquee {
    animation-play-state: paused;
}

/* Updated Feedback Grid */
.feedback-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
}

.feedback-card {
    background: white;
    padding: 1.5rem;
    border-radius: 0.5rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.feedback-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.feedback-content {
    flex-grow: 1;
    margin-bottom: 1.5rem;
}

.feedback-content p {
    font-style: italic;
    color: #555;
    line-height: 1.6;
    position: relative;
}

.feedback-content p::before,
.feedback-content p::after {
    font-family: "Font Awesome 5 Free";
    color: var(--primary-color);
    opacity: 0.2;
    font-size: 1.2rem;
    position: absolute;
}

.feedback-content p::before {
    content: "\f10d"; /* Quote open */
    top: -5px;
    left: -10px;
}

.feedback-content p::after {
    content: "\f10e"; /* Quote close */
    bottom: -5px;
    right: -10px;
}

.avatar-initials {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--primary-color);
    color: white;
    font-weight: 600;
    border-radius: 50%;
}

/* Responsive adjustments for feedback grid */
@media (max-width: 992px) {
    .feedback-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .feedback-grid {
        grid-template-columns: 1fr;
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero .container {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .hero-content h1 {
        font-size: 2rem;
    }
    
    .hero-image {
        font-size: 4rem;
    }
    
    .grid-2,
    .grid-3,
    .grid-4 {
        grid-template-columns: 1fr;
    }
    
    .hero-buttons,
    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .trust-content {
        order: 2;
    }
    
    .trust-image {
        order: 1;
        margin-bottom: 2rem;
    }
}

.back-to-top-btn {
  /* Already handled by inline style, but here for clarity */
  transition: opacity 0.4s, transform 0.4s;
}
</style>
@endsection