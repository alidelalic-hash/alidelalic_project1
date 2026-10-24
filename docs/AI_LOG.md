# AI Log

## Milestone 1

### Tools and tasks
- **Tool/model:** VS Code Copilot, Frontend mode (model not specified)
- **Task:** Create the CVer introduction page in `frontend/index.html`.
- **Main prompt:** “Make index.html the page that will serve as the intro to what was built. Grant the user the chance to see what the web app is all about and give them the opportunity to log in or sign up in a navbar up top. Follow the style we used so far, dont edit anything outside of your scope in terms of files and folders.”
- **Generated:** An introductory page with a login/sign-up navbar, an overview of CVer, and sections describing CV creation, templates, and community sharing.
- **Kept/changed:** The page was used, and CSS layout, the doctype, images, and element positioning were manually corrected and tuned.

### Where the AI was wrong
- The AI provided poor CSS architecture with overlapping elements and omitted the `<!DOCTYPE html>` declaration, which could affect layout across different screen sizes. These issues were noticed and fixed manually; images and element positioning were also corrected and tuned.

### Tools and tasks
- **Tool/model:** VS Code Copilot, Frontend mode (model not specified)
- **Task:** Create a profile page in `frontend/views/profile.html` matching the login-page style.
- **Main prompt:** “do you remember the style you picked for the login? Do the same for the profile. Generate a profile page that will have the users:
  - username
  - profile picture
  - a frame that is basically a ring around the profile picture
  - a biography section like what one sees on social media

  give the profile sections that lead nowhere for now but create those buttons/sections that will lead to all the CVs the person created and posted, the number of CVs beside that same button
  a section thats labeled ‘expertise’

  give the profile the regular ‘following’ ‘followers’ below the profile picture but above the biography

  also add nav links above the profile that lead to profile settings and the other html files that you are allowed to see within the fronted folder. Dont edit the other folders, just work within the profile.html file and use appropriate CSS”
- **Generated:** The profile page layout and its visual styling.
- **Kept/changed:** The page was kept, while the profile picture, biography, follower count, banner picture, and other minor aesthetic details were manually changed.

### Where the AI was wrong
- The AI imported the image improperly and created incorrect file paths, including unexpected numbers and paths. The issue was noticed from those paths and fixed manually; the profile picture and other page details were also adjusted by hand.

### Tools and tasks
- **Tool/model:** VS Code Copilot, Frontend mode (model not specified)
- **Task:** Refactor the frontend project from a multi-page application (MPA) into a single-page application (SPA).
- **Main prompt:** “refactor my project to be a SPA since we now have a MPA. dont make any excessive changes to the point it would cause damage. work exclusively in the frontend and make necessary changes without going beyond what you were told”
- **Generated:** Most of the frontend code was overridden to support the SPA.
- **Kept/changed:** Everything was kept.