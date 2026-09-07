import { useState, useEffect, useRef } from "react";

// Contributor identity comes from the account when the visitor is signed in, so
// collaborators never retype what their profile already holds. Mirrors the shared
// field model used by the collaborator application and the dashboard profile.
const bootConfig = typeof window !== "undefined" ? (window.RECISubmissionConfig || {}) : {};
const bootUser = bootConfig.currentUser || {};
const isSignedIn = Boolean(bootUser.isLoggedIn);

const RECMHSubmission = () => {
  const [currentStep, setCurrentStep] = useState(0);
  const [contentType, setContentType] = useState(null);
  const [selectedSpheres, setSelectedSpheres] = useState([]);
  const [expandedSphere, setExpandedSphere] = useState(null);
  const [formData, setFormData] = useState({
    title: "", abstract: "", evidenceBasis: "", processOrientation: "",
    targetAudience: [], keywords: "", contentLink: "", fileDescription: "",
    practiceType: "", equityFocus: "",
    firstName: bootUser.firstName || "",
    lastName: bootUser.lastName || "",
    email: bootUser.email || "",
    organization: bootUser.organization || "",
    role: bootUser.role || "",
    bio: bootUser.bio || "",
    website: bootUser.website || "",
    agreeTerms: false, agreeReview: false, location: "",
  });
  const [submitted, setSubmitted] = useState(false);
  const [showGuidelinesPanel, setShowGuidelinesPanel] = useState(false);
  const [hoveredType, setHoveredType] = useState(null);
  const [animateIn, setAnimateIn] = useState(true);
  const [tooltipSphere, setTooltipSphere] = useState(null);
  const [submissionFile, setSubmissionFile] = useState(null);
  const [submitError, setSubmitError] = useState("");
  const [isSubmitting, setIsSubmitting] = useState(false);
  const topRef = useRef(null);
  const submissionConfig = typeof window !== "undefined" ? (window.RECISubmissionConfig || {}) : {};

  const scrollToTop = () => {
    topRef.current?.scrollIntoView({ behavior: "smooth", block: "start" });
  };

  useEffect(() => {
    setAnimateIn(false);
    const t = setTimeout(() => setAnimateIn(true), 50);
    return () => clearTimeout(t);
  }, [currentStep]);

  const fallbackContentTypes = [
    { id: "article", label: "Magazine / Newspaper Article", icon: "✦", desc: "Long-form or short-form written pieces exploring racial equity practices, policies, or frameworks. Feature articles, op-eds, research summaries, and investigative pieces.", examples: "Feature stories, policy analyses, research-based essays, case studies", wordRange: "800 – 3,000 words" },
    { id: "blog", label: "Blog Post", icon: "◈", desc: "Accessible, conversational pieces that share insights, reflections, or practical guidance on advancing racial equity. Can be personal narrative or analytical.", examples: "Personal reflections, how-to guides, commentary, book reviews", wordRange: "400 – 1,200 words" },
    { id: "video", label: "Video Content", icon: "▶", desc: "Explainer videos, mini-documentaries, recorded presentations, interviews, or visual storytelling that advances racial equity consciousness.", examples: "Explainer videos, documentary shorts, panel recordings, testimonials", wordRange: "3 – 30 minutes" },
    { id: "podcast", label: "Podcast / Audio", icon: "◉", desc: "Audio content including interviews, discussions, storytelling, or educational episodes centered on racial equity themes.", examples: "Interview episodes, roundtable discussions, narrative audio, lecture recordings", wordRange: "15 – 60 minutes" },
    { id: "document", label: "Resource", icon: "▣", desc: "Reports, PDFs, curricula, policy briefs, toolkits, and linked materials that contributors want to share as practical standalone resources.", examples: "Research papers, reports, curricula, policy briefs, toolkits", wordRange: "Varies by format" },
    { id: "exhibit", label: "Virtual Exhibit", icon: "◇", desc: "Digital exhibitions, curated visual collections, interactive timelines, or multimedia presentations that illuminate racial equity topics.", examples: "Photo essays, digital archives, interactive timelines, curated collections", wordRange: "Varies by format" },
    { id: "assessment", label: "Assessment / Tool", icon: "⬡", desc: "Self-assessments, surveys, diagnostic instruments, or interactive tools that help users evaluate or develop their racial equity consciousness.", examples: "Self-reflection instruments, organizational audits, learning diagnostics, checklists", wordRange: "Varies by format" },
    { id: "other", label: "Other Content", icon: "✧", desc: "Infographics, curricula, toolkits, creative works, or other formats that don't fit neatly into the above categories but advance racial equity.", examples: "Infographics, curricula, training materials, creative works, data visualizations", wordRange: "Varies by format" },
  ];

  const mapContentType = (type, index) => {
    if (!type || typeof type !== "object") return null;
    const id = String(type.id || "").trim();
    if (!id) return null;
    return {
      id,
      label: String(type.label || `Content Type ${index + 1}`),
      icon: String(type.icon || "•"),
      desc: String(type.desc || ""),
      examples: String(type.examples || ""),
      wordRange: String(type.wordRange || ""),
    };
  };
  const configuredContentTypes = Array.isArray(submissionConfig.contentTypeOptions)
    ? submissionConfig.contentTypeOptions.map(mapContentType).filter(Boolean)
    : [];
  const contentTypes = configuredContentTypes.length > 0 ? configuredContentTypes : fallbackContentTypes;

  const fallbackSpheres = [
    {
      id: 1, num: "01",
      awareness: "Recognizing Racial Oppression",
      action: "Advancing Racial Liberation",
      color: "#9B4D3A",
      gradient: "linear-gradient(135deg, #9B4D3A, #C17050)",
      desc: "Content that helps individuals and communities identify the structures, systems, and mechanisms of racial oppression—and that illuminates pathways toward racial liberation.",
      guideQuestions: [
        "Does your content examine how racial oppression operates within specific systems (education, healthcare, housing, criminal justice, etc.)?",
        "Does it identify concrete structures or policies that perpetuate racial inequity?",
        "Does it offer frameworks, models, or examples of how racial liberation is being advanced?",
        "Does it center the experiences and agency of racially oppressed communities?",
      ],
      exampleTopics: "Structural racism analysis, abolition frameworks, liberation movements, decolonization, reparations discourse, anti-racist policy design",
    },
    {
      id: 2, num: "02",
      awareness: "Examining Racial Identities",
      action: "Addressing Racial Biases",
      color: "#7A6340",
      gradient: "linear-gradient(135deg, #7A6340, #A88B5C)",
      desc: "Content that supports deep examination of racial identity formation and the biases that emerge from socialization—and that provides tools for confronting and transforming those biases.",
      guideQuestions: [
        "Does your content explore how racial identities are formed, performed, or perceived?",
        "Does it address implicit or explicit racial biases and their impacts?",
        "Does it offer evidence-based approaches for identifying and mitigating racial bias?",
        "Does it help individuals understand their own racial identity in relation to others?",
      ],
      exampleTopics: "Racial identity development, implicit bias interventions, racial socialization, whiteness studies, multiracial identity, colorism",
    },
    {
      id: 3, num: "03",
      awareness: "Embracing Racial Diversity",
      action: "Growing Racial Literacy",
      color: "#4A7A5C",
      gradient: "linear-gradient(135deg, #4A7A5C, #6B9F78)",
      desc: "Content that celebrates and engages with the richness of racial diversity while building the knowledge and competencies needed to navigate racial dynamics with skill and understanding.",
      guideQuestions: [
        "Does your content promote genuine engagement with racial diversity beyond surface-level representation?",
        "Does it build racial literacy—the ability to read, interpret, and respond to racial dynamics?",
        "Does it provide frameworks for understanding cultural differences and commonalities?",
        "Does it move beyond tolerance toward meaningful inclusion and belonging?",
      ],
      exampleTopics: "Cultural competency development, racial literacy curricula, inclusive organizational design, cross-racial dialogue, multicultural education, belonging frameworks",
    },
    {
      id: 4, num: "04",
      awareness: "Building Racial Empathy",
      action: "Enhancing Racial Stamina",
      color: "#3A6B7A",
      gradient: "linear-gradient(135deg, #3A6B7A, #5A8F9E)",
      desc: "Content that cultivates the capacity to understand and share in the racial experiences of others, and that builds the endurance needed to sustain engagement with difficult racial realities over time.",
      guideQuestions: [
        "Does your content foster the ability to empathize across racial difference?",
        "Does it help people develop stamina for sustained racial equity engagement—even when it's uncomfortable?",
        "Does it address racial anxiety, fragility, or fatigue and offer strategies for resilience?",
        "Does it model what sustained, courageous engagement with racial equity looks like?",
      ],
      exampleTopics: "Racial anxiety research, racial stamina development, courageous conversations, intergroup contact theory, empathy-building practices, sustaining activism",
    },
    {
      id: 5, num: "05",
      awareness: "Acknowledging Racial Trauma",
      action: "Fostering Racial Healing",
      color: "#6A4A7A",
      gradient: "linear-gradient(135deg, #6A4A7A, #8E6EA0)",
      desc: "Content that names and validates the individual and collective trauma caused by racism, and that offers evidence-based or culturally grounded pathways toward healing and restoration.",
      guideQuestions: [
        "Does your content acknowledge the reality and depth of racial trauma—individual, intergenerational, and collective?",
        "Does it avoid retraumatization while still bearing witness to painful realities?",
        "Does it offer healing frameworks, restorative practices, or therapeutic approaches?",
        "Does it center the voices and agency of those most impacted by racial trauma?",
      ],
      exampleTopics: "Racial trauma research, healing-centered engagement, restorative justice, truth and reconciliation, intergenerational trauma, community care models",
    },
    {
      id: 6, num: "06",
      awareness: "Gauging Racial Inequities",
      action: "Championing Racial Justice",
      color: "#2C5F5A",
      gradient: "linear-gradient(135deg, #2C5F5A, #4A8078)",
      desc: "Content that measures, documents, and analyzes racial inequities with rigor—and that champions concrete actions, policies, and movements advancing racial justice.",
      guideQuestions: [
        "Does your content provide data, metrics, or analysis that illuminate the scope of racial inequities?",
        "Does it document racial disparities in specific domains with evidentiary support?",
        "Does it advocate for or evaluate specific policies, programs, or interventions advancing racial justice?",
        "Does it connect measurement of inequity to actionable change strategies?",
      ],
      exampleTopics: "Racial equity audits, disparity data analysis, policy evaluation, social impact measurement, advocacy strategies, equity scorecards, justice reform",
    },
  ];

  const mapConfigSphere = (sphere, index) => {
    if (!sphere || typeof sphere !== "object") {
      return null;
    }

    const rawQuestions = Array.isArray(sphere.guideQuestions)
      ? sphere.guideQuestions
      : String(sphere.guideQuestions || "").split(/\r?\n/);
    const guideQuestions = rawQuestions
      .map((item) => String(item || "").trim())
      .filter(Boolean);

    const termId = Number(sphere.termId || sphere.id || 0);
    const displayId = termId > 0 ? termId : (index + 1);
    return {
      id: displayId,
      termId: termId > 0 ? termId : null,
      termSlug: String(sphere.termSlug || sphere.slug || "").trim(),
      num: String(sphere.num || "").trim() || String(index + 1).padStart(2, "0"),
      awareness: String(sphere.awareness || "").trim() || `Sphere ${index + 1}`,
      action: String(sphere.action || "").trim(),
      color: String(sphere.color || "").trim() || "#9B4D3A",
      gradient: String(sphere.gradient || "").trim() || "linear-gradient(135deg, #9B4D3A, #C17050)",
      desc: String(sphere.desc || "").trim(),
      guideQuestions,
      exampleTopics: String(sphere.exampleTopics || "").trim(),
    };
  };

  const configuredSpheres = Array.isArray(submissionConfig.spheres)
    ? submissionConfig.spheres.map(mapConfigSphere).filter(Boolean)
    : [];
  const spheres = configuredSpheres.length > 0 ? configuredSpheres : fallbackSpheres;

  const mapOption = (option, index, prefix) => {
    if (typeof option === "string") {
      const name = option.trim();
      if (!name) return null;
      return { id: `${prefix}-${index}`, termId: null, name, slug: "" };
    }

    if (!option || typeof option !== "object") {
      return null;
    }

    const name = String(option.name || option.label || "").trim();
    if (!name) return null;

    const termId = Number(option.id || option.termId || 0);
    return {
      id: termId > 0 ? termId : `${prefix}-${index}`,
      termId: termId > 0 ? termId : null,
      name,
      slug: String(option.slug || "").trim(),
    };
  };

  const fallbackPracticeTypes = [
    "Practice / Intervention",
    "Policy / Legislation",
    "Program / Initiative",
    "Framework / Model",
    "Curriculum / Training",
    "Community-Based Approach",
    "Organizational Strategy",
    "Research / Evaluation",
    "Other",
  ];

  const fallbackAudienceOptions = [
    "Educators / Academics",
    "Community Organizers",
    "Policy Makers",
    "Organizational Leaders",
    "Students",
    "General Public",
    "Healthcare Professionals",
    "Social Workers",
    "Legal Professionals",
    "Faith Communities",
  ];

  const practiceTypeOptions = (Array.isArray(submissionConfig.practiceOptions) && submissionConfig.practiceOptions.length > 0
    ? submissionConfig.practiceOptions
    : fallbackPracticeTypes)
    .map((item, index) => mapOption(item, index, "practice"))
    .filter(Boolean);
  const audienceOptions = (Array.isArray(submissionConfig.targetAudienceOptions) && submissionConfig.targetAudienceOptions.length > 0
    ? submissionConfig.targetAudienceOptions
    : fallbackAudienceOptions)
    .map((item, index) => mapOption(item, index, "audience"))
    .filter(Boolean);

  const findPracticeOption = (name) => practiceTypeOptions.find((option) => option.name === name) || null;
  const findAudienceOption = (name) => audienceOptions.find((option) => option.name === name) || null;

  const steps = [
    { num: "01", label: "Content Type", short: "Type" },
    { num: "02", label: "RECI Sphere Alignment", short: "Spheres" },
    { num: "03", label: "Content Details", short: "Details" },
    { num: "04", label: "Review & Submit", short: "Review" },
  ];

  const toggleSphere = (id) => {
    setSelectedSpheres((prev) =>
      prev.includes(id) ? prev.filter((s) => s !== id) : [...prev, id]
    );
  };

  const toggleAudience = (aud) => {
    setFormData((prev) => ({
      ...prev,
      targetAudience: prev.targetAudience.includes(aud)
        ? prev.targetAudience.filter((a) => a !== aud)
        : [...prev.targetAudience, aud],
    }));
  };

  const updateForm = (field, value) => {
    setFormData((prev) => ({ ...prev, [field]: value }));
  };

  const canProceed = () => {
    switch (currentStep) {
      case 0: return contentType !== null;
      case 1: return selectedSpheres.length > 0;
      case 2: return formData.title.trim() && formData.abstract.trim() && formData.evidenceBasis.trim();
      default: return true;
    }
  };

  const goNext = () => {
    if (currentStep < steps.length - 1 && canProceed()) {
      setCurrentStep((p) => p + 1);
      scrollToTop();
    }
  };
  const goBack = () => {
    if (currentStep > 0) {
      setCurrentStep((p) => p - 1);
      scrollToTop();
    }
  };

  const resetSubmissionForm = () => {
    setSubmitted(false);
    setCurrentStep(0);
    setContentType(null);
    setSelectedSpheres([]);
    setSubmissionFile(null);
    setSubmitError("");
    setIsSubmitting(false);
    setFormData({
      title: "", abstract: "", evidenceBasis: "", processOrientation: "",
      targetAudience: [], keywords: "", contentLink: "", fileDescription: "",
      practiceType: "", equityFocus: "",
      firstName: "", lastName: "", email: "", organization: "", role: "",
      bio: "", website: "", agreeTerms: false, agreeReview: false, location: "",
    });
  };

  const handleSubmit = async () => {
    if (isSubmitting) return;

    setSubmitError("");
    setIsSubmitting(true);

    const endpoint = submissionConfig.submitEndpoint || "/wp-admin/admin-post.php";
    const nonce = submissionConfig.submitNonce || "";

    if (!nonce) {
      setSubmitError("Submission is not configured correctly. Please refresh and try again.");
      setIsSubmitting(false);
      return;
    }

    try {
      const payload = new FormData();
      payload.append("action", "reci_submit_content");
      payload.append("reci_submit_content_nonce", nonce);
      payload.append("submission_content_type", contentType || "other");
      payload.append("submission_title", formData.title || "");
      payload.append("submission_summary", formData.abstract || "");
      payload.append(
        "submission_details",
        [
          `Evidence Basis:\n${formData.evidenceBasis || ""}`,
          `Process Orientation:\n${formData.processOrientation || ""}`,
          `Racial Equity Focus:\n${formData.equityFocus || ""}`,
          `Practice / Focus Area:\n${formData.practiceType || ""}`,
          `Target Audience:\n${(formData.targetAudience || []).join(", ")}`,
          `Keywords:\n${formData.keywords || ""}`,
          `File Upload Description:\n${formData.fileDescription || ""}`,
        ].join("\n\n")
      );
      payload.append("submission_content_link", formData.contentLink || "");
      payload.append("submission_first_name", formData.firstName || "");
      payload.append("submission_last_name", formData.lastName || "");
      payload.append("submission_email", formData.email || "");
      payload.append("submission_organization", formData.organization || "");
      payload.append("submission_role", formData.role || "");
      payload.append("submission_bio", formData.bio || "");
      payload.append("submission_website", formData.website || "");
      payload.append("submission_file_description", formData.fileDescription || "");

      if (formData.practiceType) {
        const practiceOption = findPracticeOption(formData.practiceType);
        if (practiceOption?.termId) {
          payload.append("reci_practice_focus_terms[]", String(practiceOption.termId));
        } else {
          payload.append("reci_practice_focus_term_names[]", formData.practiceType);
        }
      }
      (formData.targetAudience || []).forEach((aud) => {
        const audienceOption = findAudienceOption(aud);
        if (audienceOption?.termId) {
          payload.append("reci_target_audience_terms[]", String(audienceOption.termId));
        } else {
          payload.append("reci_target_audience_term_names[]", aud);
        }
      });
      selectedSpheres.forEach((sphereId) => {
        const sphere = spheres.find((entry) => entry.id === sphereId);
        if (sphere?.termId) {
          payload.append("reci_sphere_terms[]", String(sphere.termId));
        } else if (sphere?.awareness) {
          payload.append("reci_sphere_term_names[]", sphere.awareness);
        }
      });

      if (submissionFile) {
        payload.append("submission_file", submissionFile);
      }

      const response = await fetch(endpoint, {
        method: "POST",
        body: payload,
        credentials: "same-origin",
      });

      let submitSuccess = false;
      let submitErrorCode = "";
      if (response.url) {
        const resultUrl = new URL(response.url);
        submitSuccess = resultUrl.searchParams.get("submit_success") === "1";
        submitErrorCode = resultUrl.searchParams.get("submit_error") || "";
      }

      if (!submitSuccess) {
        const errors = {
          invalid_nonce: "Security check failed. Please refresh and try again.",
          invalid_type: "Please choose a valid content type.",
          missing_fields: "Please complete all required fields.",
          collaborator_required: "You need an approved Collaborator account to submit content.",
          save_failed: "We could not save your submission. Please try again.",
        };
        setSubmitError(errors[submitErrorCode] || "We could not submit right now. Please try again.");
        setIsSubmitting(false);
        return;
      }

      setSubmitted(true);
      scrollToTop();
    } catch (error) {
      setSubmitError("Unable to connect right now. Please try again.");
    } finally {
      setIsSubmitting(false);
    }
  };

  const selectedTypeData = contentTypes.find((t) => t.id === contentType);

  return (
    <div style={{ fontFamily: "var(--wp--preset--font-family--body, 'Roboto', sans-serif)", color: "var(--ink)" }}>
      <style>{`
        :root {
          --earth-deep: #262626;
          --earth-warm: #71717A;
          --earth-gold: var(--reci-yellow, #FBBF24);
          --earth-light: #F8FAFC;
          --sage: var(--reci-blue, #1D4ED8);
          --sage-deep: var(--reci-blue, #003594);
          --cream: #F1F5F9;
          --ink: #171717;
          --terracotta: var(--reci-yellow, #FBBF24);
          --muted: #52525B;
          --border: #D4D4D8;
          --surface: var(--reci-white, #FFFFFF);
        }

        @keyframes fadeUp { from { opacity:0; transform:translateY(30px); } to { opacity:1; transform:translateY(0); } }
        @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
        @keyframes scaleIn { from { opacity:0; transform:scale(0.92); } to { opacity:1; transform:scale(1); } }
        @keyframes slideDown { from { opacity:0; transform:translateY(-12px); } to { opacity:1; transform:translateY(0); } }
        @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }
        @keyframes checkPop { 0% { transform:scale(0); } 60% { transform:scale(1.2); } 100% { transform:scale(1); } }
        @keyframes drawCircle { from { stroke-dashoffset: 283; } to { stroke-dashoffset: 0; } }
        @keyframes float { 0%,100% { transform:translateY(0); } 50% { transform:translateY(-6px); } }
        @keyframes pulseGlow { 0%,100% { box-shadow: 0 0 0 0 rgba(193,123,74,0.3); } 50% { box-shadow: 0 0 0 12px rgba(193,123,74,0); } }

        .step-fade { animation: fadeUp 0.5s ease-out forwards; }

        .texture-bg {
          background-image: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23000' fill-opacity='0.02'%3E%3Cpath d='M20 20.5V18H0v-2h20v-2l2 3-2 3z'/%3E%3C/g%3E%3C/svg%3E");
        }

        .field-label {
          font-family: var(--wp--preset--font-family--body, 'Roboto', sans-serif);
          font-size: 11px;
          font-weight: 700;
          letter-spacing: 1.8px;
          text-transform: uppercase;
          color: var(--earth-warm);
          margin-bottom: 8px;
          display: block;
        }

        .field-input {
          width: 100%;
          padding: 14px 18px;
          border: 1.5px solid var(--border);
          background: var(--surface);
          font-family: var(--wp--preset--font-family--body, 'Roboto', sans-serif);
          font-size: 15px;
          line-height: 1.6;
          color: var(--ink);
          transition: border-color 0.3s, box-shadow 0.3s;
          outline: none;
          border-radius: 0.5rem;
        }
        .field-input:focus {
          border-color: var(--terracotta);
          box-shadow: 0 0 0 3px rgba(193,123,74,0.1);
        }
        .field-input::placeholder { color: #B5ADA4; }

        .field-textarea {
          width: 100%;
          padding: 16px 18px;
          border: 1.5px solid var(--border);
          background: var(--surface);
          font-family: var(--wp--preset--font-family--body, 'Roboto', sans-serif);
          font-size: 15px;
          line-height: 1.75;
          color: var(--ink);
          resize: vertical;
          min-height: 120px;
          transition: border-color 0.3s, box-shadow 0.3s;
          outline: none;
          border-radius: 0.5rem;
        }
        .field-textarea:focus {
          border-color: var(--terracotta);
          box-shadow: 0 0 0 3px rgba(193,123,74,0.1);
        }
        .field-textarea::placeholder { color: #B5ADA4; }

        .field-hint {
          font-family: var(--wp--preset--font-family--body, 'Roboto', sans-serif);
          font-size: 12.5px;
          color: var(--muted);
          margin-top: 6px;
          line-height: 1.5;
        }

        .type-card {
          padding: 24px;
          border: 1px solid var(--border);
          background: var(--surface);
          cursor: pointer;
          transition: all 0.25s ease;
          border-radius: 0.75rem;
        }
        .type-card:hover { border-color: var(--earth-gold); transform: translateY(-2px); box-shadow: 0 8px 20px rgba(15,23,42,0.08); }
        .type-card.selected { border-color: var(--terracotta); background: #FDF8F3; }

        .sphere-card {
          padding: 28px;
          border: 1px solid var(--border);
          background: var(--surface);
          cursor: pointer;
          transition: all 0.25s ease;
          border-radius: 0.75rem;
        }
        .sphere-card:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(15,23,42,0.08); }
        .sphere-card.selected { border-color: transparent; }

        .tag-select {
          display: inline-flex;
          padding: 8px 16px;
          border: 1.5px solid var(--border);
          font-family: var(--wp--preset--font-family--body, 'Roboto', sans-serif);
          font-size: 12px;
          font-weight: 500;
          cursor: pointer;
          transition: all 0.25s;
          border-radius: 0.5rem;
          background: var(--surface);
          color: var(--muted);
        }
        .tag-select:hover { border-color: var(--earth-gold); color: var(--ink); }
        .tag-select.active { border-color: var(--terracotta); background: #FDF8F3; color: var(--terracotta); font-weight: 600; }

        .checkbox-custom {
          width: 20px; height: 20px;
          border: 2px solid var(--border);
          display: inline-flex;
          align-items: center; justify-content: center;
          cursor: pointer;
          transition: all 0.25s;
          flex-shrink: 0;
          border-radius: 0.35rem;
        }
        .checkbox-custom.checked {
          background: var(--terracotta);
          border-color: var(--terracotta);
        }

        .guide-panel {
          position: fixed;
          top: 0; right: 0; bottom: 0;
          width: 420px;
          max-width: 90vw;
          background: var(--earth-deep);
          z-index: 1000;
          overflow-y: auto;
          animation: slideFromRight 0.4s ease;
          box-shadow: -20px 0 60px rgba(0,0,0,0.2);
        }
        @keyframes slideFromRight { from { transform: translateX(100%); } to { transform: translateX(0); } }

        .review-row {
          display: flex;
          padding: 16px 0;
          border-bottom: 1px solid var(--border);
          gap: 24;
        }
        .review-label {
          font-family: var(--wp--preset--font-family--body, 'Roboto', sans-serif);
          font-size: 11px;
          font-weight: 700;
          letter-spacing: 1.5px;
          text-transform: uppercase;
          color: var(--muted);
          width: 140px;
          flex-shrink: 0;
          padding-top: 2px;
        }
        .review-value {
          font-size: 15px;
          line-height: 1.6;
          color: var(--ink);
          flex: 1;
        }

        @media (max-width: 768px) {
          .grid-2 { grid-template-columns: 1fr !important; }
          .grid-3 { grid-template-columns: 1fr !important; }
          .step-nav-desktop { display: none !important; }
          .main-content { padding: 40px 20px !important; }
          .review-row { flex-direction: column; gap: 4px; }
          .review-label { width: auto; }
        }
      `}</style>

      <div ref={topRef} />

      {/* ========== SUBMISSION CONFIRMED ========== */}
      {submitted ? (
        <div style={{ display: "flex", alignItems: "center", justifyContent: "center", padding: "24px 0 40px" }}>
          <div style={{ maxWidth: 600, textAlign: "center", animation: "fadeUp 0.8s ease" }}>
            <div style={{
              width: 100, height: 100, borderRadius: "50%", margin: "0 auto 32px",
              background: "linear-gradient(135deg, var(--sage-deep), var(--sage))",
              display: "flex", alignItems: "center", justifyContent: "center",
              animation: "pulseGlow 2s ease infinite",
            }}>
              <svg width="44" height="44" viewBox="0 0 44 44" fill="none">
                <circle cx="22" cy="22" r="20" stroke="rgba(255,255,255,0.3)" strokeWidth="2" fill="none"
                  style={{ strokeDasharray: 283, animation: "drawCircle 1s ease 0.3s both" }} />
                <path d="M14 22l6 6 10-12" stroke="white" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round"
                  style={{ strokeDasharray: 30, strokeDashoffset: 30, animation: "drawCircle 0.5s ease 1s both" }} />
              </svg>
            </div>

            <div style={{ fontFamily: "var(--wp--preset--font-family--body, 'Roboto', sans-serif)", fontSize: 12, letterSpacing: 3, textTransform: "uppercase", color: "var(--sage-deep)", marginBottom: 12, fontWeight: 600 }}>
              Submission Received
            </div>
            <h1 style={{ fontFamily: "var(--wp--preset--font-family--heading, 'Alternate Gothic ATF', 'Arial Narrow', Arial, sans-serif)", fontSize: 38, lineHeight: 1.2, marginBottom: 16, color: "var(--earth-deep)" }}>
              Thank You for Contributing to<br />RECI
            </h1>
            <p style={{ fontSize: 17, lineHeight: 1.8, color: "var(--muted)", marginBottom: 12 }}>
              Your submission, <strong style={{ color: "var(--ink)" }}>"{formData.title}"</strong>, has been received and will be reviewed by our editorial team.
            </p>
            <p style={{ fontSize: 15, lineHeight: 1.7, color: "var(--muted)", marginBottom: 32 }}>
              We evaluate all submissions for evidence-based rigor, process-orientation, and alignment with the RECI spheres of consciousness. You'll receive a response at <strong style={{ color: "var(--ink)" }}>{formData.email}</strong> within 10–15 business days.
            </p>

            <div style={{ background: "var(--earth-light)", padding: 28, marginBottom: 32, textAlign: "left", borderLeft: "4px solid var(--terracotta)" }}>
              <div style={{ fontFamily: "var(--wp--preset--font-family--body, 'Roboto', sans-serif)", fontSize: 11, fontWeight: 700, letterSpacing: 1.5, textTransform: "uppercase", color: "var(--terracotta)", marginBottom: 10 }}>What Happens Next</div>
              <div style={{ fontSize: 14, lineHeight: 1.8, color: "var(--earth-deep)" }}>
                <div style={{ marginBottom: 6 }}><strong>1.</strong> Our editorial team reviews your submission for alignment with RECI standards.</div>
                <div style={{ marginBottom: 6 }}><strong>2.</strong> If accepted, we'll work with you on any necessary revisions or enhancements.</div>
                <div style={{ marginBottom: 6 }}><strong>3.</strong> Your content is published on the RECI platform with full attribution.</div>
                <div><strong>4.</strong> Your contribution becomes part of our growing community knowledge base.</div>
              </div>
            </div>

            <div style={{ display: "flex", gap: 12, justifyContent: "center" }}>
              <button className="btn btn-md btn-primary" onClick={resetSubmissionForm}>
                Submit Another →
              </button>
              <button className="btn btn-md btn-outline-primary" onClick={() => {
                window.location.href = submissionConfig.returnUrl || "/";
              }}>
                Return to RECI ↗
              </button>
            </div>
          </div>
        </div>
      ) : (
        <>
          <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", gap: 16, marginBottom: 16, flexWrap: "wrap" }}>
            <div style={{ fontSize: 13, lineHeight: 1.6, color: "var(--muted)" }}>
              Approved Collaborators can submit here. Members can follow interests and apply to become a Collaborator.
            </div>
            <button className="btn btn-sm btn-outline-primary" onClick={() => setShowGuidelinesPanel(true)}>
              Submission Guidelines
            </button>
          </div>

          {/* ========== STEP PROGRESS ========== */}
          <div style={{
            background: "var(--surface)", borderBottom: "1px solid var(--border)",
            padding: "0 40px",
          }}>
            <div className="step-nav-desktop" style={{ maxWidth: 900, margin: "0 auto", display: "flex", alignItems: "center", justifyContent: "center", gap: 0, height: 60 }}>
              {steps.map((step, i) => (
                <div key={i} style={{ display: "flex", alignItems: "center" }}>
                  <div style={{
                    display: "flex", alignItems: "center", gap: 10, padding: "0 16px",
                    opacity: i <= currentStep ? 1 : 0.35, transition: "opacity 0.4s",
                  }}>
                    <div style={{
                      width: 28, height: 28, borderRadius: "50%", display: "flex", alignItems: "center", justifyContent: "center",
                      fontFamily: "var(--wp--preset--font-family--body, 'Roboto', sans-serif)", fontSize: 11, fontWeight: 700,
                      background: i < currentStep ? "var(--sage-deep)" : i === currentStep ? "var(--terracotta)" : "var(--border)",
                      color: i <= currentStep ? "white" : "var(--muted)",
                      transition: "all 0.4s",
                    }}>
                      {i < currentStep ? "✓" : step.num}
                    </div>
                    <span style={{
                      fontFamily: "var(--wp--preset--font-family--body, 'Roboto', sans-serif)", fontSize: 12, fontWeight: 600,
                      letterSpacing: 0.5, color: i === currentStep ? "var(--earth-deep)" : "var(--muted)",
                      transition: "color 0.4s",
                    }}>{step.label}</span>
                  </div>
                  {i < steps.length - 1 && (
                    <div style={{ width: 32, height: 1, background: i < currentStep ? "var(--sage-deep)" : "var(--border)", transition: "background 0.4s" }} />
                  )}
                </div>
              ))}
            </div>
            {/* Mobile step indicator */}
            <div style={{ display: "none", maxWidth: 900, margin: "0 auto", padding: "12px 0", alignItems: "center", justifyContent: "space-between" }}>
              <span style={{ fontFamily: "var(--wp--preset--font-family--body, 'Roboto', sans-serif)", fontSize: 12, fontWeight: 600, color: "var(--earth-deep)" }}>
                Step {currentStep + 1} of {steps.length}: {steps[currentStep].label}
              </span>
              <div style={{ display: "flex", gap: 4 }}>
                {steps.map((_, i) => (
                  <div key={i} style={{ width: 24, height: 3, background: i <= currentStep ? "var(--terracotta)" : "var(--border)", transition: "background 0.3s", borderRadius: 2 }} />
                ))}
              </div>
            </div>
          </div>

          {/* ========== MAIN FORM ========== */}
          <main className="main-content" style={{ maxWidth: 980, margin: "0 auto", padding: "32px 32px 48px", background: "var(--surface)", border: "1px solid var(--border)", borderRadius: 12, boxShadow: "0 1px 3px rgba(15,23,42,0.05)", marginTop: "10px"}}>
            <div className={animateIn ? "step-fade" : ""} style={{ opacity: animateIn ? undefined : 0 }}>

              {/* ===== STEP 0: CONTENT TYPE ===== */}
              {currentStep === 0 && (
                <div>
                  <div style={{ marginBottom: 36 }}>
                    <h2 style={{ fontFamily: "var(--wp--preset--font-family--heading, 'Alternate Gothic ATF', 'Arial Narrow', Arial, sans-serif)", fontSize: 30, marginBottom: 8 }}>What type of content are you submitting?</h2>
                    <p style={{ fontSize: 16, lineHeight: 1.7, color: "var(--muted)", maxWidth: 600 }}>
                      Select the format that best represents your contribution. All content should be evidence-based and process-oriented, focused on practices, policies, programs, initiatives, frameworks, or documents advancing racial equity.
                    </p>
                  </div>

                  <div style={{ display: "flex", flexDirection: "column", gap: 12 }}>
                    {contentTypes.map((type) => (
                      <div key={type.id}
                        className={`type-card ${contentType === type.id ? "selected" : ""}`}
                        onClick={() => setContentType(type.id)}
                        onMouseEnter={() => setHoveredType(type.id)}
                        onMouseLeave={() => setHoveredType(null)}
                      >
                        <div style={{ display: "flex", alignItems: "flex-start", gap: 20 }}>
                          <div style={{
                            width: 48, height: 48, display: "flex", alignItems: "center", justifyContent: "center",
                            background: contentType === type.id ? "var(--terracotta)" : "var(--earth-light)",
                            color: contentType === type.id ? "white" : "var(--earth-warm)",
                            fontFamily: "var(--wp--preset--font-family--heading, 'Alternate Gothic ATF', 'Arial Narrow', Arial, sans-serif)", fontSize: 20, flexShrink: 0,
                            transition: "all 0.35s", borderRadius: 2,
                          }}>{type.icon}</div>
                          <div style={{ flex: 1 }}>
                            <div style={{ display: "flex", alignItems: "center", justifyContent: "space-between", marginBottom: 4 }}>
                              <h3 style={{ fontFamily: "var(--wp--preset--font-family--heading, 'Alternate Gothic ATF', 'Arial Narrow', Arial, sans-serif)", fontSize: 19 }}>{type.label}</h3>
                              {contentType === type.id && (
                                <div style={{
                                  width: 22, height: 22, borderRadius: "50%", background: "var(--terracotta)",
                                  display: "flex", alignItems: "center", justifyContent: "center",
                                  color: "white", fontSize: 12, animation: "checkPop 0.3s ease",
                                }}>✓</div>
                              )}
                            </div>
                            <p style={{ fontSize: 14, lineHeight: 1.65, color: "var(--muted)", marginBottom: 8 }}>{type.desc}</p>
                            <div style={{
                              display: "flex", gap: 16, flexWrap: "wrap",
                              maxHeight: (hoveredType === type.id || contentType === type.id) ? 60 : 0,
                              overflow: "hidden", transition: "max-height 0.4s ease",
                            }}>
                              <span style={{ fontFamily: "var(--wp--preset--font-family--body, 'Roboto', sans-serif)", fontSize: 11, color: "var(--sage-deep)" }}>
                                <strong>Examples:</strong> {type.examples}
                              </span>
                              <span style={{ fontFamily: "var(--wp--preset--font-family--body, 'Roboto', sans-serif)", fontSize: 11, color: "var(--terracotta)", fontWeight: 600 }}>
                                {type.wordRange}
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>
              )}

              {/* ===== STEP 1: RECI SPHERES ===== */}
              {currentStep === 1 && (
                <div>
                  <div style={{ marginBottom: 12 }}>
                    <h2 style={{ fontFamily: "var(--wp--preset--font-family--heading, 'Alternate Gothic ATF', 'Arial Narrow', Arial, sans-serif)", fontSize: 30, marginBottom: 8 }}>Which RECI spheres does your content align with?</h2>
                    <p style={{ fontSize: 16, lineHeight: 1.7, color: "var(--muted)", maxWidth: 640 }}>
                      The Racial Equity Consciousness Institute's six spheres represent complementary dimensions of consciousness development. Select one or more spheres that your content addresses. Use the guiding questions to help determine alignment.
                    </p>
                  </div>

                  <div style={{
                    background: "#FDF8F3", border: "1px solid var(--border)", padding: "16px 20px", marginBottom: 32,
                    display: "flex", alignItems: "flex-start", gap: 12,
                  }}>
                    <span style={{ fontSize: 18, flexShrink: 0, marginTop: 1 }}>💡</span>
                    <div style={{ fontSize: 13.5, lineHeight: 1.65, color: "var(--earth-deep)" }}>
                      <strong>Most content aligns with 1–3 spheres.</strong> Each sphere has both an awareness dimension (recognizing, examining, embracing, building, acknowledging, gauging) and an action dimension (advancing, addressing, growing, enhancing, fostering, championing). Click any sphere to view detailed guiding questions.
                    </div>
                  </div>

                  <div style={{ display: "flex", flexDirection: "column", gap: 16 }}>
                    {spheres.map((sphere) => {
                      const isSelected = selectedSpheres.includes(sphere.id);
                      const isExpanded = expandedSphere === sphere.id;
                      return (
                        <div key={sphere.id}
                          className={`sphere-card ${isSelected ? "selected" : ""}`}
                          style={{
                            borderColor: isSelected ? "transparent" : undefined,
                            boxShadow: isSelected ? `0 0 0 2px ${sphere.color}, 0 8px 32px ${sphere.color}15` : undefined,
                          }}
                        >
                          <div style={{ display: "flex", alignItems: "flex-start", gap: 20 }}
                            onClick={() => toggleSphere(sphere.id)}>
                            <div style={{
                              width: 56, height: 56, borderRadius: "50%", flexShrink: 0,
                              background: isSelected ? sphere.gradient : "var(--earth-light)",
                              display: "flex", alignItems: "center", justifyContent: "center",
                              fontFamily: "var(--wp--preset--font-family--heading, 'Alternate Gothic ATF', 'Arial Narrow', Arial, sans-serif)", fontSize: 16,
                              color: isSelected ? "white" : "var(--earth-warm)",
                              transition: "all 0.4s",
                              boxShadow: isSelected ? `0 6px 20px ${sphere.color}40` : "none",
                            }}>{sphere.num}</div>
                            <div style={{ flex: 1 }}>
                              <div style={{ display: "flex", alignItems: "center", justifyContent: "space-between", marginBottom: 2 }}>
                                <div>
                                  <h3 style={{ fontFamily: "var(--wp--preset--font-family--heading, 'Alternate Gothic ATF', 'Arial Narrow', Arial, sans-serif)", fontSize: 18, lineHeight: 1.3, marginBottom: 2 }}>
                                    {sphere.awareness}
                                  </h3>
                                  <h3 style={{ fontFamily: "var(--wp--preset--font-family--heading, 'Alternate Gothic ATF', 'Arial Narrow', Arial, sans-serif)", fontSize: 18, lineHeight: 1.3, color: sphere.color }}>
                                    {sphere.action}
                                  </h3>
                                </div>
                                <div style={{
                                  width: 26, height: 26, borderRadius: "50%",
                                  border: isSelected ? "none" : "2px solid var(--border)",
                                  background: isSelected ? sphere.color : "transparent",
                                  display: "flex", alignItems: "center", justifyContent: "center",
                                  color: "white", fontSize: 13, transition: "all 0.3s",
                                  animation: isSelected ? "checkPop 0.3s ease" : "none",
                                }}>{isSelected ? "✓" : ""}</div>
                              </div>
                              <p style={{ fontSize: 14, lineHeight: 1.65, color: "var(--muted)", marginTop: 8 }}>{sphere.desc}</p>
                            </div>
                          </div>

                          <div style={{ marginTop: 12, paddingLeft: 76, display: "flex", alignItems: "center", gap: 8 }}>
                            <button onClick={(e) => { e.stopPropagation(); setExpandedSphere(isExpanded ? null : sphere.id); }}
                              style={{
                                background: "none", border: "none", cursor: "pointer",
                                fontFamily: "var(--wp--preset--font-family--body, 'Roboto', sans-serif)", fontSize: 12, fontWeight: 600,
                                color: sphere.color, display: "flex", alignItems: "center", gap: 4,
                                transition: "opacity 0.3s",
                              }}>
                              {isExpanded ? "Hide" : "View"} Guiding Questions
                              <span style={{ transform: isExpanded ? "rotate(180deg)" : "rotate(0deg)", transition: "transform 0.3s", display: "inline-block" }}>▾</span>
                            </button>
                          </div>

                          {isExpanded && (
                            <div style={{
                              marginTop: 16, marginLeft: 76, padding: 24,
                              background: `${sphere.color}08`, borderLeft: `3px solid ${sphere.color}`,
                              animation: "slideDown 0.3s ease",
                            }}>
                              <div style={{ fontFamily: "var(--wp--preset--font-family--body, 'Roboto', sans-serif)", fontSize: 11, fontWeight: 700, letterSpacing: 1.5, textTransform: "uppercase", color: sphere.color, marginBottom: 12 }}>
                                Guiding Questions for Alignment
                              </div>
                              {sphere.guideQuestions.map((q, qi) => (
                                <div key={qi} style={{ display: "flex", gap: 10, marginBottom: 10, alignItems: "flex-start" }}>
                                  <span style={{ color: sphere.color, fontWeight: 700, fontSize: 14, marginTop: 1, flexShrink: 0 }}>→</span>
                                  <span style={{ fontSize: 14, lineHeight: 1.65, color: "var(--ink)" }}>{q}</span>
                                </div>
                              ))}
                              <div style={{ marginTop: 14, paddingTop: 12, borderTop: `1px solid ${sphere.color}20` }}>
                                <div style={{ fontFamily: "var(--wp--preset--font-family--body, 'Roboto', sans-serif)", fontSize: 11, fontWeight: 700, letterSpacing: 1.2, textTransform: "uppercase", color: "var(--muted)", marginBottom: 4 }}>Example Topics</div>
                                <div style={{ fontSize: 13, lineHeight: 1.6, color: "var(--muted)", fontStyle: "italic" }}>{sphere.exampleTopics}</div>
                              </div>
                            </div>
                          )}
                        </div>
                      );
                    })}
                  </div>

                  {selectedSpheres.length > 0 && (
                    <div style={{
                      marginTop: 24, padding: "14px 20px",
                      background: "var(--earth-light)", display: "flex", alignItems: "center", gap: 8,
                      animation: "fadeIn 0.3s ease",
                    }}>
                      <span style={{ fontFamily: "var(--wp--preset--font-family--body, 'Roboto', sans-serif)", fontSize: 12, color: "var(--earth-warm)", fontWeight: 600 }}>
                        Selected: {selectedSpheres.map((id) => spheres.find((s) => s.id === id)?.awareness).join(" · ")}
                      </span>
                    </div>
                  )}
                </div>
              )}

              {/* ===== STEP 2: CONTENT DETAILS ===== */}
              {currentStep === 2 && (
                <div>
                  <div style={{ marginBottom: 36 }}>
                    <h2 style={{ fontFamily: "var(--wp--preset--font-family--heading, 'Alternate Gothic ATF', 'Arial Narrow', Arial, sans-serif)", fontSize: 30, marginBottom: 8 }}>Tell us about your content</h2>
                    <p style={{ fontSize: 16, lineHeight: 1.7, color: "var(--muted)", maxWidth: 600 }}>
                      Provide details about the practice, policy, program, initiative, or framework your content addresses. Fields marked with * are required.
                    </p>
                  </div>

                  <div style={{ display: "flex", flexDirection: "column", gap: 28 }}>
                    <div>
                      <label className="field-label">Title *</label>
                      <input className="field-input" placeholder="Enter the title of your submission"
                        value={formData.title} onChange={(e) => updateForm("title", e.target.value)} />
                    </div>

                    <div>
                      <label className="field-label">Type of Practice / Focus Area</label>
                      <div style={{ display: "flex", flexWrap: "wrap", gap: 8 }}>
                        {practiceTypeOptions.map((pt) => (
                          <button key={pt.id} className={`tag-select ${formData.practiceType === pt.name ? "active" : ""}`}
                            onClick={() => updateForm("practiceType", formData.practiceType === pt.name ? "" : pt.name)}>
                            {pt.name}
                          </button>
                        ))}
                      </div>
                    </div>

                    <div>
                      <label className="field-label">Abstract / Summary *</label>
                      <textarea className="field-textarea" placeholder="Provide a summary of your content (200–400 words). Describe the practice, policy, program, initiative, or framework you're addressing, its context, and its significance for advancing racial equity."
                        value={formData.abstract} onChange={(e) => updateForm("abstract", e.target.value)}
                        style={{ minHeight: 160 }} />
                      <div className="field-hint">Describe the core focus, context, and significance of your content for advancing racial equity.</div>
                    </div>

                    <div style={{ background: "#FDF8F3", border: "1px solid var(--border)", padding: 28 }}>
                      <div style={{ fontFamily: "var(--wp--preset--font-family--body, 'Roboto', sans-serif)", fontSize: 11, fontWeight: 700, letterSpacing: 1.5, textTransform: "uppercase", color: "var(--terracotta)", marginBottom: 16 }}>
                        Evidence & Process Orientation
                      </div>

                      <div style={{ marginBottom: 20 }}>
                        <label className="field-label">Evidence Basis *</label>
                        <textarea className="field-textarea" placeholder="Describe the evidence base supporting your content. This could include research findings, data, documented outcomes, evaluation results, peer-reviewed sources, practitioner evidence, or community-based participatory research."
                          value={formData.evidenceBasis} onChange={(e) => updateForm("evidenceBasis", e.target.value)}
                          style={{ background: "white" }} />
                        <div className="field-hint">What evidence demonstrates that the practice, policy, or framework you're presenting is effective?</div>
                      </div>

                      <div>
                        <label className="field-label">Process Orientation</label>
                        <textarea className="field-textarea" placeholder="Explain how your content is process-oriented. How does it describe or guide a developmental process? Does it outline stages, steps, or phases of growth? Does it emphasize ongoing learning rather than one-time interventions?"
                          value={formData.processOrientation} onChange={(e) => updateForm("processOrientation", e.target.value)}
                          style={{ background: "white" }} />
                        <div className="field-hint">RECI values content that emphasizes process and development over static outcomes.</div>
                      </div>
                    </div>

                    <div>
                      <label className="field-label">Racial Equity Focus</label>
                      <textarea className="field-textarea" placeholder="Describe specifically how your content addresses racial equity. What racial equity challenge does it respond to? What change does it aim to create?"
                        value={formData.equityFocus} onChange={(e) => updateForm("equityFocus", e.target.value)} />
                    </div>

                    <div>
                      <label className="field-label">Target Audience</label>
                      <div style={{ display: "flex", flexWrap: "wrap", gap: 8 }}>
                        {audienceOptions.map((aud) => (
                          <button key={aud.id} className={`tag-select ${formData.targetAudience.includes(aud.name) ? "active" : ""}`}
                            onClick={() => toggleAudience(aud.name)}>
                            {aud.name}
                          </button>
                        ))}
                      </div>
                    </div>

                    <div className="grid-2" style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 20 }}>
                      <div>
                        <label className="field-label">Keywords / Tags</label>
                        <input className="field-input" placeholder="e.g., restorative justice, implicit bias, K-12"
                          value={formData.keywords} onChange={(e) => updateForm("keywords", e.target.value)} />
                        <div className="field-hint">Separate with commas.</div>
                      </div>
                      <div>
                        <label className="field-label">Content Link (if applicable)</label>
                        <input className="field-input" placeholder="https://..."
                          value={formData.contentLink} onChange={(e) => updateForm("contentLink", e.target.value)} />
                        <div className="field-hint">Link to hosted video, podcast, exhibit, or document.</div>
                      </div>
                    </div>

                    <div>
                      <label className="field-label">Upload File (optional)</label>
                      <input className="field-input" type="file"
                        onChange={(e) => setSubmissionFile(e.target.files?.[0] || null)} />
                      <div className="field-hint">Attach a document, manuscript, media file, or supplementary file.</div>
                    </div>

                    <div>
                      <label className="field-label">File Upload Description</label>
                      <textarea className="field-textarea" placeholder="If you plan to upload files (manuscripts, media files, supplementary materials), describe them here. Our team will follow up with upload instructions after initial review."
                        value={formData.fileDescription} onChange={(e) => updateForm("fileDescription", e.target.value)}
                        style={{ minHeight: 80 }} />
                    </div>
                  </div>
                </div>
              )}

              {/* ===== STEP 3: CONTRIBUTOR INFO ===== */}
              {/* ===== STEP 4: REVIEW & CONFIRM ===== */}
              {currentStep === 3 && (
                <div>
                  <div style={{ marginBottom: 36 }}>
                    <h2 style={{ fontFamily: "var(--wp--preset--font-family--heading, 'Alternate Gothic ATF', 'Arial Narrow', Arial, sans-serif)", fontSize: 30, marginBottom: 8 }}>Review Your Submission</h2>
                    <p style={{ fontSize: 16, lineHeight: 1.7, color: "var(--muted)", maxWidth: 600 }}>
                      Please review your submission details below before sending. You can go back to any step to make changes.
                    </p>
                  </div>

                  <div style={{ border: "1px solid var(--border)", background: "var(--surface)" }}>
                    {/* Content Type */}
                    <div style={{ padding: "24px 28px", borderBottom: "1px solid var(--border)", background: "#FDF8F3" }}>
                      <div style={{ fontFamily: "var(--wp--preset--font-family--body, 'Roboto', sans-serif)", fontSize: 11, fontWeight: 700, letterSpacing: 1.5, textTransform: "uppercase", color: "var(--terracotta)", marginBottom: 8 }}>Content Type</div>
                      <div style={{ fontFamily: "var(--wp--preset--font-family--heading, 'Alternate Gothic ATF', 'Arial Narrow', Arial, sans-serif)", fontSize: 20 }}>
                        {selectedTypeData?.label}
                      </div>
                    </div>

                    {/* Spheres */}
                    <div style={{ padding: "24px 28px", borderBottom: "1px solid var(--border)" }}>
                      <div style={{ fontFamily: "var(--wp--preset--font-family--body, 'Roboto', sans-serif)", fontSize: 11, fontWeight: 700, letterSpacing: 1.5, textTransform: "uppercase", color: "var(--terracotta)", marginBottom: 12 }}>RECI Sphere Alignment</div>
                      <div style={{ display: "flex", flexWrap: "wrap", gap: 10 }}>
                        {selectedSpheres.map((id) => {
                          const s = spheres.find((sp) => sp.id === id);
                          return (
                            <div key={id} style={{
                              display: "flex", alignItems: "center", gap: 10, padding: "8px 14px",
                              background: `${s.color}10`, border: `1.5px solid ${s.color}30`, borderRadius: 2,
                            }}>
                              <div style={{
                                width: 24, height: 24, borderRadius: "50%", background: s.gradient,
                                display: "flex", alignItems: "center", justifyContent: "center",
                                fontSize: 9, color: "white", fontWeight: 700,
                              }}>{s.num}</div>
                              <div>
                                <div style={{ fontSize: 12, fontWeight: 600, color: s.color, lineHeight: 1.2 }}>{s.awareness}</div>
                                <div style={{ fontSize: 12, fontWeight: 600, color: "var(--ink)", lineHeight: 1.2 }}>{s.action}</div>
                              </div>
                            </div>
                          );
                        })}
                      </div>
                    </div>

                    {/* Content Details */}
                    <div style={{ padding: "24px 28px", borderBottom: "1px solid var(--border)" }}>
                      <div style={{ fontFamily: "var(--wp--preset--font-family--body, 'Roboto', sans-serif)", fontSize: 11, fontWeight: 700, letterSpacing: 1.5, textTransform: "uppercase", color: "var(--terracotta)", marginBottom: 16 }}>Content Details</div>

                      <div className="review-row"><div className="review-label">Title</div><div className="review-value" style={{ fontFamily: "var(--wp--preset--font-family--heading, 'Alternate Gothic ATF', 'Arial Narrow', Arial, sans-serif)", fontSize: 18 }}>{formData.title}</div></div>
                      {formData.practiceType && <div className="review-row"><div className="review-label">Focus Area</div><div className="review-value">{formData.practiceType}</div></div>}
                      <div className="review-row"><div className="review-label">Abstract</div><div className="review-value">{formData.abstract}</div></div>
                      <div className="review-row"><div className="review-label">Evidence Basis</div><div className="review-value">{formData.evidenceBasis}</div></div>
                      {formData.processOrientation && <div className="review-row"><div className="review-label">Process Focus</div><div className="review-value">{formData.processOrientation}</div></div>}
                      {formData.equityFocus && <div className="review-row"><div className="review-label">Equity Focus</div><div className="review-value">{formData.equityFocus}</div></div>}
                      {formData.targetAudience.length > 0 && <div className="review-row"><div className="review-label">Audience</div><div className="review-value">{formData.targetAudience.join(", ")}</div></div>}
                      {formData.keywords && <div className="review-row"><div className="review-label">Keywords</div><div className="review-value">{formData.keywords}</div></div>}
                      {formData.contentLink && <div className="review-row"><div className="review-label">Content Link</div><div className="review-value" style={{ color: "var(--terracotta)" }}>{formData.contentLink}</div></div>}
                    </div>

                    {/* Contributor */}
                    <div style={{ padding: "24px 28px" }}>
                      <div style={{ fontFamily: "var(--wp--preset--font-family--body, 'Roboto', sans-serif)", fontSize: 11, fontWeight: 700, letterSpacing: 1.5, textTransform: "uppercase", color: "var(--terracotta)", marginBottom: 16 }}>Contributor</div>
                      <div className="review-row"><div className="review-label">Name</div><div className="review-value">{formData.firstName} {formData.lastName}</div></div>
                      <div className="review-row"><div className="review-label">Email</div><div className="review-value">{formData.email}</div></div>
                      {formData.organization && <div className="review-row"><div className="review-label">Organization</div><div className="review-value">{formData.organization}</div></div>}
                      {formData.role && <div className="review-row"><div className="review-label">Role</div><div className="review-value">{formData.role}</div></div>}
                      {formData.bio && <div className="review-row" style={{ borderBottom: "none" }}><div className="review-label">Bio</div><div className="review-value">{formData.bio}</div></div>}
                    </div>

                  {/* Per-submission consent: belongs with what is being consented to. */}
                      <div style={{ borderTop: "1px solid var(--border)", paddingTop: 24, display: "flex", flexDirection: "column", gap: 16 }}>
                        <div style={{ display: "flex", gap: 12, alignItems: "flex-start", cursor: "pointer" }}
                          onClick={() => updateForm("agreeTerms", !formData.agreeTerms)}>
                          <div className={`checkbox-custom ${formData.agreeTerms ? "checked" : ""}`}>
                            {formData.agreeTerms && <span style={{ color: "white", fontSize: 12, lineHeight: 1 }}>✓</span>}
                          </div>
                          <div style={{ fontSize: 14, lineHeight: 1.6, color: "var(--ink)" }}>
                            <strong>I confirm</strong> that this content is my original work (or I have permission to submit it), that it is evidence-based and/or process-oriented, and that it has not been previously published on the RECI platform. *
                          </div>
                        </div>

                        <div style={{ display: "flex", gap: 12, alignItems: "flex-start", cursor: "pointer" }}
                          onClick={() => updateForm("agreeReview", !formData.agreeReview)}>
                          <div className={`checkbox-custom ${formData.agreeReview ? "checked" : ""}`}>
                            {formData.agreeReview && <span style={{ color: "white", fontSize: 12, lineHeight: 1 }}>✓</span>}
                          </div>
                          <div style={{ fontSize: 14, lineHeight: 1.6, color: "var(--ink)" }}>
                            <strong>I understand</strong> that all submissions undergo editorial review for alignment with RECI's mission, evidence standards, and the RECI framework, and that the editorial team may suggest revisions. *
                          </div>
                        </div>
                      </div>
                  </div>
                </div>
              )}

            </div>

            {/* ===== NAVIGATION BUTTONS ===== */}
            {submitError && (
              <div style={{
                marginBottom: 18,
                padding: "12px 14px",
                border: "1px solid #F3B6B6",
                background: "#FFF5F5",
                color: "#8B1E1E",
                fontSize: 14,
                lineHeight: 1.5,
              }}>
                {submitError}
              </div>
            )}
            <div style={{
              display: "flex", justifyContent: "space-between", alignItems: "center",
              marginTop: 40, paddingTop: 32, borderTop: "1px solid var(--border)",
            }}>
              <div>
                {currentStep > 0 && (
                  <button className="btn btn-md btn-outline-primary" onClick={goBack}>← Back</button>
                )}
              </div>
              <div style={{ display: "flex", alignItems: "center", gap: 16 }}>
                {currentStep < steps.length - 1 ? (
                  <button className="btn btn-md btn-primary" onClick={goNext} disabled={!canProceed()}>
                    Continue to {steps[currentStep + 1].short} →
                  </button>
                ) : (
                  <button className="btn btn-md btn-primary" onClick={handleSubmit} disabled={isSubmitting || !formData.agreeTerms || !formData.agreeReview}>
                    {isSubmitting ? "Submitting..." : "Submit for Review ✓"}
                  </button>
                )}
              </div>
            </div>
          </main>
        </>
      )}

      {/* ========== GUIDELINES PANEL ========== */}
      {showGuidelinesPanel && (
        <>
          <div style={{ position: "fixed", top: 0, left: 0, right: 0, bottom: 0, background: "rgba(0,0,0,0.4)", zIndex: 999, animation: "fadeIn 0.3s ease" }}
            onClick={() => setShowGuidelinesPanel(false)} />
          <div className="guide-panel">
            <div style={{ padding: "28px 32px", borderBottom: "1px solid rgba(255,255,255,0.08)", display: "flex", justifyContent: "space-between", alignItems: "center" }}>
              <h2 style={{ fontFamily: "var(--wp--preset--font-family--heading, 'Alternate Gothic ATF', 'Arial Narrow', Arial, sans-serif)", fontSize: 22, color: "#FAF7F2" }}>Submission Guidelines</h2>
              <button onClick={() => setShowGuidelinesPanel(false)} style={{ background: "none", border: "none", color: "rgba(250,247,242,0.5)", fontSize: 22, cursor: "pointer" }}>✕</button>
            </div>
            <div style={{ padding: "28px 32px", overflowY: "auto" }}>
              {[
                {
                  title: "What We're Looking For",
                  content: "RECI curates content that is evidence-based and process-oriented, focused on practices, policies, programs, initiatives, frameworks, and resources that effectively advance racial equity. We seek contributions that inform individuals, communities, and organizations—and ultimately inspire them to share what they've learned."
                },
                {
                  title: "Evidence-Based Standard",
                  content: "Submissions should be grounded in evidence. This includes peer-reviewed research, documented outcomes, evaluation data, practitioner knowledge, community-based participatory research, or other credible evidentiary foundations. Claims should be supported, and sources cited where possible."
                },
                {
                  title: "Process Orientation",
                  content: "We value content that emphasizes process and developmental growth over static facts or one-time interventions. Content should guide readers/viewers/listeners through a journey of understanding—modeling the kind of ongoing consciousness development RECI champions."
                },
                {
                  title: "RECI Sphere Alignment",
                  content: "All content should align with at least one of RECI's six spheres of consciousness development. Each sphere has both an awareness dimension and an action dimension, reflecting the journey from recognition to transformation."
                },
                {
                  title: "Content Standards",
                  content: "All submissions should be original or properly attributed. Content should be respectful, constructive, and grounded in a commitment to advancing racial equity. We welcome diverse perspectives and encourage submissions from contributors of all backgrounds and career stages."
                },
                {
                  title: "Editorial Process",
                  content: "All submissions are reviewed by the RECI editorial team for quality, evidence standards, process orientation, and alignment with the RECI framework. We may suggest revisions to strengthen your contribution. Expect a response within 10–15 business days."
                },
                {
                  title: "Accepted Formats",
                  content: "Magazine articles (800–3,000 words), blog posts (400–1,200 words), video (3–30 min), podcasts (15–60 min), resources, virtual exhibits, assessments/tools, infographics, curricula, and other creative formats that advance racial equity consciousness."
                },
              ].map((section, i) => (
                <div key={i} style={{ marginBottom: 24 }}>
                  <h3 style={{ fontFamily: "var(--wp--preset--font-family--heading, 'Alternate Gothic ATF', 'Arial Narrow', Arial, sans-serif)", fontSize: 16, color: "var(--earth-gold)", marginBottom: 8 }}>{section.title}</h3>
                  <p style={{ fontSize: 13.5, lineHeight: 1.7, color: "rgba(250,247,242,0.6)" }}>{section.content}</p>
                </div>
              ))}

              <div style={{ marginTop: 32, padding: "20px 24px", background: "rgba(250,247,242,0.05)", borderLeft: "3px solid var(--terracotta)" }}>
                <div style={{ fontFamily: "var(--wp--preset--font-family--body, 'Roboto', sans-serif)", fontSize: 11, fontWeight: 700, letterSpacing: 1.5, textTransform: "uppercase", color: "var(--terracotta)", marginBottom: 8 }}>Questions?</div>
                <p style={{ fontSize: 13, lineHeight: 1.6, color: "rgba(250,247,242,0.6)" }}>
                  Contact the RECI editorial team for guidance on your submission. We're happy to help you determine the best format and sphere alignment for your content.
                </p>
              </div>
            </div>
          </div>
        </>
      )}


    </div>
  );
};

export default RECMHSubmission;
