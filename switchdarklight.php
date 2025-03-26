function custom_dark_light_mode_switch_navbar() {
    ob_start();
    ?>
    <style>
      input[type="checkbox"] {
        display: none;
      }

      #switch {
        width: 100px;
        height: 40px;
        background: #FF438F;
        box-shadow: 3px 1px 4px -1px #FFFFFF80 inset;
        border-radius: 20px;
        position: relative;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.4s ease;
      }

      #circle {
        width: 36px;
        height: 36px;
        background-color: white;
        border-radius: 50%;
        position: absolute;
        left: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.4s ease left;
      }

      #circle svg {
        width: 27px;
        height: 27px;
      }

      #text {
        color: white;
        font-size: 14px;
        font-weight: bold;
        position: relative;
        transition: 0.4s ease color;
      }

      /* Dark Mode Styling */
      #toggle:checked + #switch {
        background-color: #251938;
      }

      #toggle:checked + #switch #circle {
        left: 60px;
      }

      #toggle:checked + #switch #text {
        color: white;
        left: -15px;
      }

      #toggle:checked + #switch #text::before {
        content: "Light";
      }

      #toggle:not(:checked) + #switch #text::before {
        content: "Dark";
      }

      /* Arabic Text Style */
      .arabic #toggle:checked + #switch #text::before {
        content: "الفاتح";
      }

      .arabic #text {
        font-size: 16px;
      }

      .arabic #toggle:not(:checked) + #switch #text::before {
        content: "الداكن";
      }

      .arabic #circle {
        right: 61px;
      }

      .arabic #toggle:checked + #switch #circle {
        right: 4px;
      }

      .arabic #toggle:checked + #switch #text {
        right: 17px;
      }

      #toggle:checked + #switch #circle svg {
        fill: #ff007a;
      }

      #toggle:not(:checked) + #switch #circle svg {
        fill: #212121;
      }

      /* Navbar positioning */
      .navbar-dark-light-toggle {
        display: inline-block;
        margin-left: 20px;
        vertical-align: middle;
      }
    </style>

    <div class="navbar-dark-light-toggle">
      <input type="checkbox" id="toggle" />
      <label id="switch" for="toggle">
        <div id="text"></div>
        <div id="circle">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" id="icon">
            <path id="moon" d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z"></path>
            <circle id="sun" cx="12" cy="12" r="5" style="display: none"></circle>
          </svg>
        </div>
      </label>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.0/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.0/ScrollTrigger.min.js"></script>

    <script>
      (function () {
        // Function to set a cookie
        function setCookie(name, value, days) {
          const expires = new Date();
          expires.setTime(expires.getTime() + days * 24 * 60 * 60 * 1000);
          document.cookie = name + "=" + value + ";expires=" + expires.toUTCString() + ";path=/";
        }

        // Function to get a cookie
        function getCookie(name) {
          const cookies = document.cookie.split("; ");
          for (let i = 0; i < cookies.length; i++) {
            const [key, val] = cookies[i].split("=");
            if (key === name) {
              return val;
            }
          }
          return null;
        }

        function light_dark_mode() {
          const darkmode = gsap.timeline({ paused: true });
          const tl = gsap.timeline();

          // Define animations for light mode elements
          tl.to("body.light .e-parent", { backgroundColor: "#FFF", duration: 0.1 });
          tl.set(".has-logo-image img", { attr: { src: "https://new.themiify.com/wp-content/uploads/2024/10/Frame-3-1.png" } }, "<");
          tl.set(".footer-width-fixer img", { attr: { src: "https://new.themiify.com/wp-content/uploads/2024/10/Frame-3-1.png" } }, "<");
          tl.set(".card-content .theme-link img", { attr: { src: "https://new.themiify.com/wp-content/uploads/2024/10/vuesax.svg" } }, "<");

          darkmode.add(tl);

          const button = document.getElementById("toggle");
          const body = document.querySelector("body");

          let currentTheme = getCookie("theme");
          if (!currentTheme) {
            currentTheme = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            setCookie("theme", currentTheme, 7);
          }

          if (currentTheme === "dark") {
            darkmode.play();
            button.checked = true;
            body.classList.add("light");
          } else {
            body.classList.remove("light");
          }

          button.addEventListener("change", () => {
            if (button.checked) {
              darkmode.play();
              setCookie("theme", "dark", 7);
              body.classList.add("light");
            } else {
              darkmode.reverse();
              setCookie("theme", "light", 7);
              body.classList.remove("light");
            }
          });

          const isArabic = window.location.href.includes("/ar");
          if (isArabic) {
            document.body.classList.add("arabic");
          }
        }

        light_dark_mode();
      })();
    </script>
    <?php
    return ob_get_clean();
}

add_filter('wp_nav_menu_items', function ($items, $args) {
    if ($args->theme_location == 'primary' || $args->theme_location == 'mobile') {
        $items .= custom_dark_light_mode_switch_navbar();
    }
    return $items;
}, 10, 2);

add_action('init', function () {
    if (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark') {
        add_filter('body_class', function ($classes) {
            $classes[] = 'light';
            return $classes;
        });
    }
});
