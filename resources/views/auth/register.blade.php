<!DOCTYPE html>
<html class="light" lang="en">
<head>
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Join ReWear - The Living Archive</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200;400;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "primary": "#173124",
            "outline-variant": "#c2c8c2",
            "surface-container-low": "#f4f4f1",
            "primary-container": "#2d4739",
            "surface-container-highest": "#e2e3e0",
            "inverse-surface": "#2f312f",
            "tertiary-fixed": "#ede1c9",
            "secondary-fixed": "#ffdbcf",
            "on-tertiary": "#ffffff",
            "on-background": "#1a1c1b",
            "primary-fixed-dim": "#b0cdbb",
            "primary-fixed": "#ccead6",
            "tertiary-container": "#484130",
            "surface-variant": "#e2e3e0",
            "on-secondary-fixed": "#380d00",
            "inverse-on-surface": "#f1f1ee",
            "surface-container-lowest": "#ffffff",
            "on-error": "#ffffff",
            "inverse-primary": "#b0cdbb",
            "on-primary": "#ffffff",
            "on-tertiary-fixed-variant": "#4d4634",
            "surface-dim": "#dadad7",
            "on-primary-fixed-variant": "#324c3e",
            "error": "#ba1a1a",
            "tertiary-fixed-dim": "#d1c5ae",
            "surface-container": "#eeeeeb",
            "surface": "#f9f9f6",
            "on-tertiary-fixed": "#211b0c",
            "surface-bright": "#f9f9f6",
            "outline": "#727973",
            "error-container": "#ffdad6",
            "tertiary": "#312b1b",
            "on-primary-fixed": "#062014",
            "background": "#f9f9f6",
            "on-tertiary-container": "#b8ad97",
            "on-primary-container": "#98b5a3",
            "on-secondary-fixed-variant": "#75331b",
            "on-surface": "#1a1c1b",
            "secondary": "#924a2f",
            "on-secondary-container": "#78361d",
            "on-error-container": "#93000a",
            "on-surface-variant": "#424844",
            "on-secondary": "#ffffff",
            "secondary-fixed-dim": "#ffb59b",
            "secondary-container": "#fea181",
            "surface-tint": "#496455",
            "surface-container-high": "#e8e8e5"
          },
          borderRadius: {
            "DEFAULT": "0.25rem",
            "lg": "0.5rem",
            "xl": "0.75rem",
            "full": "9999px"
          },
          spacing: {
            "1.5": "0.375rem",
            "20": "5rem"
          },
          fontFamily: {
            "headline": ["Manrope"],
            "body": ["Inter"],
            "label": ["Inter"]
          }
        },
      },
    }
  </script>
  <style>
    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    .editorial-shadow {
      box-shadow: 0 20px 40px -15px rgba(26, 28, 27, 0.06);
    }
    .input-error {
      outline: 2px solid #ba1a1a;
    }
  </style>
</head>

<body class="bg-surface font-body text-on-surface antialiased overflow-x-hidden">
  <main class="min-h-screen flex flex-col md:flex-row">
    <section class="relative w-full md:w-5/12 min-h-[450px] md:min-h-screen flex items-center justify-center p-8 md:p-12 overflow-hidden bg-primary">
      <div class="absolute inset-0 z-0">
        <img alt="Join the Circular Movement" class="w-full h-full object-cover opacity-60" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBMFhtC2CbSEQ3w11ig6Kp5YBRsoMJiBwoxxc4_z0lOxAxQmRoYHs6h8eX8X00Icyg6YZZxvAZMacNKmZaFflonvNU5qZGnkIvs-2kDIUOjRbJ3OFJGwTwjuboNZw5GNc1lBYeQ-EKcGjGVmFXfdiSLAlDnS-mwnbV53C5TC0Eh1VNtEClmB-nXRbztcPpOs2PvmOU8nxFFz-lZP_z4Fr5YSuBIiWH6X8DzT2dn8C1GQp6Pj4-H9ozFImmKr1yYTe0i925tgqMb4zw" />
        <div class="absolute inset-0 bg-gradient-to-t from-primary via-transparent to-transparent opacity-80"></div>
      </div>

      <div class="relative z-10 max-w-sm md:max-w-md text-center md:text-left space-y-6">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary-fixed/20 backdrop-blur-md text-primary-fixed border border-primary-fixed/10">
          <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">eco</span>
          <span class="text-xs font-label font-semibold tracking-wider uppercase">Est. 2024</span>
        </div>

        <h1 class="font-headline font-extrabold text-5xl md:text-6xl text-white leading-[1.1] tracking-tighter">
          Join the <span class="text-primary-fixed">Circular</span> Movement
        </h1>

        <p class="text-white/80 font-body text-lg leading-relaxed">
          ReWear is more than a platform. It's a living archive where fashion stories never end.
        </p>

        <div class="pt-8 flex flex-col gap-5">
          <div class="flex items-center gap-4 text-white/90">
            <div class="w-10 h-10 shrink-0 rounded-full bg-white/10 flex items-center justify-center backdrop-blur-sm">
              <span class="material-symbols-outlined text-primary-fixed">inventory_2</span>
            </div>
            <span class="font-label text-sm font-medium">Share your sustainable fashion journey</span>
          </div>
          <div class="flex items-center gap-4 text-white/90">
            <div class="w-10 h-10 shrink-0 rounded-full bg-white/10 flex items-center justify-center backdrop-blur-sm">
              <span class="material-symbols-outlined text-primary-fixed">public</span>
            </div>
            <span class="font-label text-sm font-medium">Reduce your carbon footprint</span>
          </div>
        </div>
      </div>
    </section>

    <section class="w-full md:w-7/12 flex flex-col justify-center items-center bg-surface px-6 py-16 md:px-16 lg:px-24">
      <div class="w-full max-w-[440px] space-y-10">
        <div class="space-y-3">
          <div class="text-primary text-3xl font-headline font-extrabold tracking-tighter">ReWear</div>
          <h2 class="font-headline text-3xl text-on-surface font-bold tracking-tight">Create Account</h2>
          <p class="text-on-surface-variant font-body text-md">
            Start your sustainable fashion journey today.
          </p>
        </div>

        <form id="registerForm" class="space-y-6">
          <div class="space-y-5">
            <div class="space-y-2">
              <label class="block text-xs font-label font-bold text-on-surface-variant tracking-wider uppercase" for="name">Full Name</label>
              <input class="w-full bg-surface-container-highest border-none rounded-lg px-4 py-3.5 text-on-surface placeholder:text-on-surface-variant/40 focus:ring-2 focus:ring-primary-fixed transition-all duration-300" id="name" placeholder="Iqbal Abhipraya" type="text" />
            </div>

            <div class="space-y-2">
              <label class="block text-xs font-label font-bold text-on-surface-variant tracking-wider uppercase" for="email">Email Address</label>
              <input class="w-full bg-surface-container-highest border-none rounded-lg px-4 py-3.5 text-on-surface placeholder:text-on-surface-variant/40 focus:ring-2 focus:ring-primary-fixed transition-all duration-300" id="email" placeholder="hello@example.com" type="email" />
            </div>

            <div class="space-y-2">
              <label class="block text-xs font-label font-bold text-on-surface-variant tracking-wider uppercase" for="password">Password</label>
              <div class="relative">
                <input class="w-full bg-surface-container-highest border-none rounded-lg px-4 py-3.5 text-on-surface placeholder:text-on-surface-variant/40 focus:ring-2 focus:ring-primary-fixed transition-all duration-300" id="password" placeholder="••••••••" type="password" />
                <button id="togglePassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant/60 hover:text-primary transition-colors" type="button">
                  <span id="togglePasswordIcon" class="material-symbols-outlined text-xl">visibility</span>
                </button>
              </div>
            </div>
          </div>

          <div class="space-y-4 pt-2">
            <label class="flex items-start gap-3 cursor-pointer group">
              <div class="relative flex items-center justify-center mt-0.5">
                <input id="terms" class="peer appearance-none w-5 h-5 border-2 border-outline rounded-md checked:bg-primary checked:border-primary transition-all duration-200" type="checkbox" />
                <span class="material-symbols-outlined absolute text-white text-[16px] hidden peer-checked:block pointer-events-none">check</span>
              </div>
              <span class="text-sm font-body text-on-surface-variant leading-relaxed select-none">
                I agree to the <a class="text-primary font-semibold underline decoration-primary-fixed underline-offset-4" href="#">Terms & Privacy</a> policy.
              </span>
            </label>

            <label class="flex items-start gap-3 cursor-pointer group">
              <div class="relative flex items-center justify-center mt-0.5">
                <input id="newsletter" checked class="peer appearance-none w-5 h-5 border-2 border-outline rounded-md checked:bg-primary checked:border-primary transition-all duration-200" type="checkbox" />
                <span class="material-symbols-outlined absolute text-white text-[16px] hidden peer-checked:block pointer-events-none">check</span>
              </div>
              <span class="text-sm font-body text-on-surface-variant leading-relaxed select-none">
                Subscribe to sustainable fashion tips and exclusive archive access.
              </span>
            </label>
          </div>

          <p id="formMessage" class="hidden text-sm font-medium"></p>

          <div class="pt-4">
            <button id="submitBtn" class="w-full py-4 rounded-full bg-gradient-to-r from-primary to-primary-container text-white font-headline font-bold text-lg hover:brightness-110 active:scale-[0.98] transition-all duration-300 editorial-shadow" type="submit">
              Create Account
            </button>
          </div>
        </form>

        <div class="flex flex-col items-center gap-6 pt-2">
          <p class="text-on-surface-variant font-body text-sm">
            Already have an account? <a class="text-primary font-bold hover:underline underline-offset-4" href="{{ route('login') }}">Sign In</a>
          </p>
        </div>
      </div>
    </section>
  </main>

  <footer class="w-full py-8 bg-surface-container-low flex flex-col md:flex-row justify-between items-center px-8 md:px-12 gap-6">
    <div class="text-xl font-bold text-primary tracking-tighter">ReWear.</div>
    <p class="text-on-surface-variant/60 font-body text-sm text-center md:text-left">
      © 2024 ReWear. The Living Archive of Fashion.
    </p>
    <div class="flex gap-8">
      <a class="text-on-surface-variant/60 hover:text-primary transition-colors text-sm font-medium" href="#">Sustainability</a>
      <a class="text-on-surface-variant/60 hover:text-primary transition-colors text-sm font-medium" href="#">Privacy</a>
      <a class="text-on-surface-variant/60 hover:text-primary transition-colors text-sm font-medium" href="#">Contact</a>
    </div>
  </footer>

  <script>
    const form = document.getElementById("registerForm");
    const nameInput = document.getElementById("name");
    const emailInput = document.getElementById("email");
    const passwordInput = document.getElementById("password");
    const termsInput = document.getElementById("terms");
    const newsletterInput = document.getElementById("newsletter");
    const submitBtn = document.getElementById("submitBtn");
    const formMessage = document.getElementById("formMessage");

    const togglePasswordBtn = document.getElementById("togglePassword");
    const togglePasswordIcon = document.getElementById("togglePasswordIcon");

    const googleBtn = document.getElementById("googleBtn");
    const appleBtn = document.getElementById("appleBtn");

    function showMessage(message, isError = true) {
      formMessage.textContent = message;
      formMessage.classList.remove("hidden", "text-error", "text-green-700");
      formMessage.classList.add(isError ? "text-error" : "text-green-700");
    }

    function clearErrors() {
      [nameInput, emailInput, passwordInput].forEach(input => {
        input.classList.remove("input-error");
      });
      formMessage.classList.add("hidden");
      formMessage.textContent = "";
    }

    function isValidEmail(email) {
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    togglePasswordBtn.addEventListener("click", () => {
      const isPassword = passwordInput.type === "password";
      passwordInput.type = isPassword ? "text" : "password";
      togglePasswordIcon.textContent = isPassword ? "visibility_off" : "visibility";
    });

    form.addEventListener("submit", async (e) => {
      e.preventDefault();
      clearErrors();

      const name = nameInput.value.trim();
      const email = emailInput.value.trim();
      const password = passwordInput.value.trim();
      const termsAccepted = termsInput.checked;

      let hasError = false;

      if (!name) {
        nameInput.classList.add("input-error");
        hasError = true;
      }

      if (!email || !isValidEmail(email)) {
        emailInput.classList.add("input-error");
        hasError = true;
      }

      if (!password || password.length < 8) { // Default Laravel password length is 8
        passwordInput.classList.add("input-error");
        hasError = true;
      }

      if (!termsAccepted) {
        hasError = true;
        showMessage("You must agree to the Terms & Privacy policy.");
      }

      if (hasError) return;

      submitBtn.disabled = true;
      submitBtn.textContent = "Creating Account...";
      submitBtn.classList.add("opacity-70", "cursor-not-allowed");

      // Mengambil CSRF Token dari meta tag
      const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

      try {
        const response = await fetch('/register', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          },
          body: JSON.stringify({
            name: name,
            email: email,
            password: password,
            password_confirmation: password // Diperlukan oleh validasi default Laravel
          })
        });

        if (response.ok) {
          showMessage("Account created successfully! Redirecting...", false);
          setTimeout(() => {
            window.location.href = "{{ url('/') }}";
          }, 1500);
        } else {
          // Menangkap dan menampilkan error validasi dari Laravel
          const data = await response.json();
          let errorMessage = data.message || "Something went wrong. Please try again.";

          if (data.errors) {
             errorMessage = Object.values(data.errors)[0][0]; // Ambil pesan error pertama
          }
          showMessage(errorMessage);

          submitBtn.disabled = false;
          submitBtn.textContent = "Create Account";
          submitBtn.classList.remove("opacity-70", "cursor-not-allowed");
        }
      } catch (error) {
        showMessage("Network error. Please try again.");
        submitBtn.disabled = false;
        submitBtn.textContent = "Create Account";
        submitBtn.classList.remove("opacity-70", "cursor-not-allowed");
      }
    });

    googleBtn.addEventListener("click", () => {
      window.location.href = "/auth/google";
    });

    appleBtn.addEventListener("click", () => {
      window.location.href = "/auth/apple";
    });
  </script>
</body>
</html>
