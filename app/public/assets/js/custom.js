function initMap() {
            // Coordinates for Desa Klambu, Grobogan
            const klambuCoords = [-7.0069338,110.7955922];
            
            // Create map
            const map = L.map('map').setView(klambuCoords, 13);
            
            // Add tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            // Add marker
            L.marker(klambuCoords).addTo(map)
                .bindPopup('<b>Maulia MUA</b><br>Desa Klambu, Grobogan<br>Jawa Tengah')
                .openPopup();
        }
        
        // Initialize map when page loads
        document.addEventListener('DOMContentLoaded', initMap);
        
        // Back to top button functionality
        const backToTopButton = document.getElementById('backToTop');
        
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.add('show');
            } else {
                backToTopButton.classList.remove('show');
            }
        });
        
        backToTopButton.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                    
                    // Close mobile menu if open
                    const navbarToggler = document.querySelector('.navbar-toggler');
                    const navbarCollapse = document.querySelector('.navbar-collapse');
                    if (navbarCollapse.classList.contains('show')) {
                        navbarToggler.click();
                    }
                }
            });
        });
        
        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            if (window.pageYOffset > 50) {
                navbar.style.padding = '10px 0';
                navbar.style.boxShadow = '0 5px 20px rgba(0, 0, 0, 0.08)';
            } else {
                navbar.style.padding = '15px 0';
                navbar.style.boxShadow = '0 2px 20px rgba(149, 157, 165, 0.1)';
            }
        });
        
        // Auto-select package when clicking package buttons
        document.querySelectorAll('[data-package]').forEach(button => {
            button.addEventListener('click', function(e) {
                if (!this.getAttribute('href') || this.getAttribute('href') === '#booking') {
                    e.preventDefault();
                    const packageValue = this.getAttribute('data-package');
                    document.getElementById('package').value = packageValue;
                    
                    // Scroll to booking form
                    document.querySelector('#booking').scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Form submission
        const bookingForm = document.getElementById('bookingForm');
        bookingForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            // Get form values
            const fullName = document.getElementById('fullName').value;
            const phone = document.getElementById('phone').value;
            const serviceType = document.getElementById('serviceType').value;
            const package = document.getElementById('package').value;
            const eventDate = document.getElementById('eventDate').value;
            const location = document.getElementById('location').value;
            const additionalInfo = document.getElementById('additionalInfo').value;
            
            // Format date
            const formattedDate = new Date(eventDate).toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            // Create WhatsApp message
            const message = `Halo Maulia, saya ${fullName} ingin booking layanan pernikahan. Detail:
📋 Jenis Layanan: ${serviceType}
📦 Paket: ${package}
📅 Tanggal Acara: ${formattedDate}
📍 Lokasi Acara: ${location}
📞 Telepon: ${phone}
📝 Info Tambahan: ${additionalInfo || '-'}

Saya sudah membaca syarat dan ketentuan.`;
            
            // Encode message for URL
            const encodedMessage = encodeURIComponent(message);
            
            // Create WhatsApp URL
            const whatsappURL = `https://wa.me/6287731310979?text=${encodedMessage}`;
            
            // Open WhatsApp
            window.open(whatsappURL, '_blank');
            
            // Show success message
            alert('Terima kasih! Form Anda telah berhasil dikirim. Anda akan diarahkan ke WhatsApp untuk konfirmasi lebih lanjut.');
            
            // Reset form
            bookingForm.reset();
        });
        
        // Set minimum date for event date (tomorrow)
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        const minDate = tomorrow.toISOString().split('T')[0];
        document.getElementById('eventDate').min = minDate;
        
        // Highlight active nav link on scroll
        const sections = document.querySelectorAll('section[id]');
        
        window.addEventListener('scroll', () => {
            const scrollY = window.pageYOffset;
            
            sections.forEach(section => {
                const sectionHeight = section.offsetHeight;
                const sectionTop = section.offsetTop - 100;
                const sectionId = section.getAttribute('id');
                const navLink = document.querySelector(`.navbar-nav a[href="#${sectionId}"]`);
                
                if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
                    navLink.classList.add('active');
                } else {
                    navLink.classList.remove('active');
                }
            });
        });