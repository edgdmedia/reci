import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import App from './App';
import type { Blueprint } from '../../types/blueprint';

const root = document.getElementById('reci-reflection-root');
if (!root) throw new Error('Reflection root element not found');

// Read the blueprint JSON blob embedded by PHP
const dataEl = document.getElementById('reci-reflection-experience-data');
if (!dataEl?.textContent) throw new Error('Reflection experience data not found');

const blueprint = JSON.parse(dataEl.textContent) as Blueprint;

// Read post ID from WordPress body class (e.g. "postid-123")
const postIdMatch = document.body.className.match(/postid-(\d+)/);
const postId = postIdMatch ? parseInt(postIdMatch[1], 10) : 0;

createRoot(root).render(
  <StrictMode>
    <App blueprint={blueprint} postId={postId} />
  </StrictMode>,
);
