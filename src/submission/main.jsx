import React from "react";
import { createRoot } from "react-dom/client";
import RECMHSubmission from "../../sample/recmh-submission.jsx";

const mountNode = document.getElementById("reci-submission-root");

if (mountNode) {
  createRoot(mountNode).render(
    <React.StrictMode>
      <RECMHSubmission />
    </React.StrictMode>
  );
}
