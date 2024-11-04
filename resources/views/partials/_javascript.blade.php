    <script>
        var subdir = "{{ Config::get('app.subdir') }}";
        var loggedin = "{{ Auth::check() }}";
    </script>
    <script type="module">
        import { FumaSetup } from "{{ Vite::appjs('fuma.js') }}";
        $(function(){
            console.log("Setting up fuma timeout");
            FumaSetup(loggedin);
        });
    </script>



