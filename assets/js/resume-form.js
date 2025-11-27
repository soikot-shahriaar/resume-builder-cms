/**
 * Resume Form JavaScript
 * Handles dynamic form sections for education, experience, skills, and projects
 */

let educationCount = 0;
let experienceCount = 0;
let skillsCount = 0;
let projectsCount = 0;

// Initialize form on page load
document.addEventListener('DOMContentLoaded', function() {
    // Add initial entries
    addEducation();
    addExperience();
    addSkill();
    addProject();
});

// Education functions
function addEducation() {
    const container = document.getElementById('education-container');
    const index = educationCount++;
    
    const educationHTML = `
        <div class="dynamic-item" id="education-${index}">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Institution</label>
                    <input type="text" class="form-control" name="education[${index}][institution]" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Degree</label>
                    <input type="text" class="form-control" name="education[${index}][degree]" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Field of Study</label>
                    <input type="text" class="form-control" name="education[${index}][field_of_study]">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">GPA</label>
                    <input type="text" class="form-control" name="education[${index}][gpa]" placeholder="3.8">
                </div>
                <div class="col-md-3 mb-3">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="education[${index}][current]" 
                               id="education-current-${index}" onchange="toggleEndDate('education', ${index})">
                        <label class="form-check-label" for="education-current-${index}">
                            Currently studying
                        </label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" class="form-control" name="education[${index}][start_date]">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">End Date</label>
                    <input type="date" class="form-control" name="education[${index}][end_date]" 
                           id="education-end-date-${index}">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="education[${index}][description]" rows="3" 
                          placeholder="Relevant coursework, achievements, honors..."></textarea>
            </div>
            <button type="button" class="btn btn-sm remove-item" onclick="removeEducation(${index})">
                <i class="bi bi-trash"></i> Remove
            </button>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', educationHTML);
}

function removeEducation(index) {
    const element = document.getElementById(`education-${index}`);
    if (element) {
        element.remove();
    }
}

// Experience functions
function addExperience() {
    const container = document.getElementById('experience-container');
    const index = experienceCount++;
    
    const experienceHTML = `
        <div class="dynamic-item" id="experience-${index}">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Company</label>
                    <input type="text" class="form-control" name="experience[${index}][company]" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Position</label>
                    <input type="text" class="form-control" name="experience[${index}][position]" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Location</label>
                    <input type="text" class="form-control" name="experience[${index}][location]" 
                           placeholder="City, State">
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="experience[${index}][current]" 
                               id="experience-current-${index}" onchange="toggleEndDate('experience', ${index})">
                        <label class="form-check-label" for="experience-current-${index}">
                            Currently working here
                        </label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" class="form-control" name="experience[${index}][start_date]">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">End Date</label>
                    <input type="date" class="form-control" name="experience[${index}][end_date]" 
                           id="experience-end-date-${index}">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="experience[${index}][description]" rows="4" 
                          placeholder="Describe your responsibilities and achievements..."></textarea>
            </div>
            <button type="button" class="btn btn-sm remove-item" onclick="removeExperience(${index})">
                <i class="bi bi-trash"></i> Remove
            </button>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', experienceHTML);
}

function removeExperience(index) {
    const element = document.getElementById(`experience-${index}`);
    if (element) {
        element.remove();
    }
}

// Skills functions
function addSkill() {
    const container = document.getElementById('skills-container');
    const index = skillsCount++;
    
    const skillHTML = `
        <div class="dynamic-item" id="skill-${index}">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Skill Name</label>
                    <input type="text" class="form-control" name="skills[${index}][skill_name]" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Level</label>
                    <select class="form-control" name="skills[${index}][skill_level]">
                        <option value="Beginner">Beginner</option>
                        <option value="Intermediate" selected>Intermediate</option>
                        <option value="Advanced">Advanced</option>
                        <option value="Expert">Expert</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Category</label>
                    <input type="text" class="form-control" name="skills[${index}][category]" 
                           placeholder="e.g., Programming, Design">
                </div>
            </div>
            <button type="button" class="btn btn-sm remove-item" onclick="removeSkill(${index})">
                <i class="bi bi-trash"></i> Remove
            </button>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', skillHTML);
}

function removeSkill(index) {
    const element = document.getElementById(`skill-${index}`);
    if (element) {
        element.remove();
    }
}

// Projects functions
function addProject() {
    const container = document.getElementById('projects-container');
    const index = projectsCount++;
    
    const projectHTML = `
        <div class="dynamic-item" id="project-${index}">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Project Name</label>
                    <input type="text" class="form-control" name="projects[${index}][project_name]" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Technologies</label>
                    <input type="text" class="form-control" name="projects[${index}][technologies]" 
                           placeholder="e.g., PHP, MySQL, JavaScript">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" class="form-control" name="projects[${index}][start_date]">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">End Date</label>
                    <input type="date" class="form-control" name="projects[${index}][end_date]">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Project URL</label>
                    <input type="url" class="form-control" name="projects[${index}][project_url]" 
                           placeholder="https://example.com">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">GitHub URL</label>
                    <input type="url" class="form-control" name="projects[${index}][github_url]" 
                           placeholder="https://github.com/username/project">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="projects[${index}][description]" rows="3" 
                          placeholder="Describe the project and your role..."></textarea>
            </div>
            <button type="button" class="btn btn-sm remove-item" onclick="removeProject(${index})">
                <i class="bi bi-trash"></i> Remove
            </button>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', projectHTML);
}

function removeProject(index) {
    const element = document.getElementById(`project-${index}`);
    if (element) {
        element.remove();
    }
}

// Toggle end date based on current status
function toggleEndDate(section, index) {
    const checkbox = document.getElementById(`${section}-current-${index}`);
    const endDateField = document.getElementById(`${section}-end-date-${index}`);
    
    if (checkbox && endDateField) {
        if (checkbox.checked) {
            endDateField.disabled = true;
            endDateField.value = '';
        } else {
            endDateField.disabled = false;
        }
    }
}

// Form validation
document.getElementById('resumeForm').addEventListener('submit', function(e) {
    const requiredFields = this.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Please fill in all required fields.');
    }
});

