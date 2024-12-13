import { tutorialDownloadVariant } from "{{ Vite::appjs('utils/tutorial_utils.js') }}";
window.tutorialDownloadVariant = tutorialDownloadVariant;
window.loggedin = "{{ Auth::check() }}";