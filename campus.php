<?php
// This is campus.php
include('includes/header.php');

// Security: Must be logged in
if (!isLoggedIn()) {
    header('Location: login.php?message=Please log in to enter the virtual campus.');
    exit;
}
?>

<script type_-"module" src="https.cdnjs.cloudflare.com/ajax/libs/three.js/0.160.0/three.module.min.js"></script>
<script type_-"module" src="https://unpkg.com/three@0.160.0/examples/jsm/controls/OrbitControls.js"></script>
<script type_-"module" src="https://unpkg.com/three@0.160.0/examples/jsm/renderers/CSS2DRenderer.js"></script>

<style>
    body {
        /* Remove all margin and overflow to make the canvas full-screen */
        margin: 0;
        overflow: hidden;
    }
    
    /* We need to hide the standard header/footer on this page */
    nav, footer {
        display: none !important;
    }
    
    /* This is the 3D canvas */
    #campus-canvas {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(#0f0c29, #302b63); /* Night sky */
    }
    
    /* This is for the HTML labels on the 3D objects */
    .label {
        color: #FFF;
        font-family: 'Inter', sans-serif;
        font-size: 16px;
        padding: 5px 10px;
        background: rgba(0, 0, 0, 0.6);
        border-radius: 5px;
        pointer-events: none; /* So we can click 'through' them */
        text-shadow: 0 0 5px #000;
    }
    
    /* This is the instruction panel */
    #instructions {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        padding: 15px 25px;
        background: rgba(255, 255, 255, 0.8);
        color: #333;
        border-radius: 10px;
        font-weight: 600;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        z-index: 100;
    }
    
    /* A simple "Back" button */
    #back-button {
        position: fixed;
        top: 20px;
        left: 20px;
        padding: 10px 15px;
        background: rgba(0, 0, 0, 0.5);
        color: #FFF;
        border-radius: 5px;
        z-index: 100;
        text-decoration: none;
        font-weight: 600;
        transition: background 0.3s;
    }
    #back-button:hover {
        background: rgba(0, 0, 0, 0.8);
    }
</style>

<a href="dashboard.php" id="back-button"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>

<div id="instructions">
    <i class="fas fa-mouse"></i> Click & Drag to orbit. Scroll to zoom. Click a building to enter.
</div>

<canvas id="campus-canvas"></canvas>

<script type="module">
    // Import all necessary modules
    import * as THREE from 'https://cdnjs.cloudflare.com/ajax/libs/three.js/0.160.0/three.module.min.js';
    import { OrbitControls } from 'https://unpkg.com/three@0.160.0/examples/jsm/controls/OrbitControls.js';
    import { CSS2DRenderer, CSS2DObject } from 'https://unpkg.com/three@0.160.0/examples/jsm/renderers/CSS2DRenderer.js';

    let scene, camera, renderer, labelRenderer, controls;
    const raycaster = new THREE.Raycaster();
    const mouse = new THREE.Vector2();
    const clickableObjects = []; // To store our 'buildings'

    // 1. --- INITIALIZE ---
    function init() {
        // Scene
        scene = new THREE.Scene();

        // Camera
        camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        camera.position.set(0, 20, 30); // Set initial position

        // WebGL Renderer (for the 3D objects)
        const canvas = document.getElementById('campus-canvas');
        renderer = new THREE.WebGLRenderer({ 
            canvas: canvas,
            antialias: true
        });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setPixelRatio(window.devicePixelRatio);
        renderer.shadowMap.enabled = true;

        // CSS2D Renderer (for the HTML labels)
        labelRenderer = new CSS2DRenderer();
        labelRenderer.setSize(window.innerWidth, window.innerHeight);
        labelRenderer.domElement.style.position = 'absolute';
        labelRenderer.domElement.style.top = '0px';
        labelRenderer.domElement.style.pointerEvents = 'none'; // IMPORTANT
        document.body.appendChild(labelRenderer.domElement);
        
        // Controls
        controls = new OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.dampingFactor = 0.05;
        controls.minDistance = 10;
        controls.maxDistance = 100;
        controls.maxPolarAngle = Math.PI / 2.1; // Don't let camera go underground

        // Lighting
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
        scene.add(ambientLight);

        const dirLight = new THREE.DirectionalLight(0xffffff, 1);
        dirLight.position.set(5, 10, 7);
        dirLight.castShadow = true;
        scene.add(dirLight);

        // --- 2. CREATE CAMPUS ---
        
        // Floor
        const floorGeo = new THREE.PlaneGeometry(100, 100);
        const floorMat = new THREE.MeshStandardMaterial({ color: 0x334433 });
        const floor = new THREE.Mesh(floorGeo, floorMat);
        floor.rotation.x = -Math.PI / 2;
        floor.receiveShadow = true;
        scene.add(floor);

        // Building 1: Lecture Hall (Courses)
        const coursesBuilding = createBuilding(
            { x: -15, y: 5, z: 0 }, // Position
            { w: 10, h: 10, d: 10 }, // Size
            0xAA4A4A, // Color
            'Lecture Hall', // Label
            'courses.php' // Link
        );
        scene.add(coursesBuilding);

        // Building 2: Library (Research)
        const researchBuilding = createBuilding(
            { x: 15, y: 6, z: 0 },
            { w: 8, h: 12, d: 8 },
            0x4A4AAA,
            'Library',
            'research.php'
        );
        scene.add(researchBuilding);

        // Building 3: Student Union (Community/Events)
        const communityBuilding = createBuilding(
            { x: 0, y: 4, z: -20 },
            { w: 12, h: 8, d: 12 },
            0x4AAA4A,
            'Student Union',
            'community.php'
        );
        scene.add(communityBuilding);
        
        // --- 3. EVENT LISTENERS ---
        window.addEventListener('resize', onWindowResize);
        window.addEventListener('click', onMouseClick);
        
        // Start the animation loop
        animate();
    }

    // --- 4. HELPER FUNCTIONS ---

    // Creates a building mesh
    function createBuilding(pos, size, color, name, link) {
        const geo = new THREE.BoxGeometry(size.w, size.h, size.d);
        const mat = new THREE.MeshStandardMaterial({ color: color });
        const mesh = new THREE.Mesh(geo, mat);
        
        mesh.position.set(pos.x, pos.y, pos.z);
        mesh.castShadow = true;
        
        // Store link for click event
        mesh.userData.link = link; 
        
        // Create the label
        const labelDiv = document.createElement('div');
        labelDiv.className = 'label';
        labelDiv.textContent = name;
        const cssLabel = new CSS2DObject(labelDiv);
        cssLabel.position.set(0, (size.h / 2) + 2, 0); // Position above the building
        mesh.add(cssLabel);
        
        clickableObjects.push(mesh);
        return mesh;
    }

    // Handle window resize
    function onWindowResize() {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
        labelRenderer.setSize(window.innerWidth, window.innerHeight);
    }
    
    // Handle mouse click
    function onMouseClick(event) {
        // Calculate mouse position in normalized device coordinates
        mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
        mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;
        
        // Update the raycaster
        raycaster.setFromCamera(mouse, camera);
        
        // Check for intersections
        const intersects = raycaster.intersectObjects(clickableObjects);
        
        if (intersects.length > 0) {
            // We clicked an object! Get the first one.
            const clickedObject = intersects[0].object;
            if (clickedObject.userData.link) {
                // Redirect to the stored link
                window.location.href = clickedObject.userData.link;
            }
        }
    }

    // --- 5. ANIMATION LOOP ---
    function animate() {
        requestAnimationFrame(animate);
        
        controls.update(); // Update controls for smooth damping
        
        renderer.render(scene, camera);
        labelRenderer.render(scene, camera);
    }

    // Run it!
    init();
</script>

<?php
// We don't include footer.php here because it's a full-screen 3D app
?>