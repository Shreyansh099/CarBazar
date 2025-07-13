<?php
$pageTitle = "Our Showrooms";
// Capture the page-specific CSS to be injected into the header
ob_start();
?>
<style>
    .showroom-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); 
        gap: 2rem; 
    }
    .showroom-card { 
        position: relative;
        /* --- THIS IS THE KEY CHANGE FOR THE RECTANGLE SHAPE --- */
        /* We use aspect-ratio to create a responsive rectangle. 16 / 9 is a widescreen ratio. */
        aspect-ratio: 16 / 9; 
        
        border-radius: 8px; 
        overflow: hidden; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.2); 
        color: white; 
        text-decoration: none; 
        display: flex;
        align-items: flex-end;
    }
    .showroom-card:hover { 
        transform: translateY(-10px); 
        transition: transform 0.3s ease;
    }
    .showroom-card-bg-image { 
        position: absolute;
        top: 0; left: 0;
        width: 100%; 
        height: 100%; 
        object-fit: cover; /* This makes the image fill the rectangle */
        z-index: 1;
        filter: brightness(0.6); 
    }
    .showroom-card .overlay { 
        position: relative;
        z-index: 2;
        width: 100%;
        padding: 1.5rem; 
        background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0) 100%); 
    }
    .showroom-card .overlay h3 { 
        margin: 0; 
        font-size: 1.8rem; 
        text-shadow: 2px 2px 4px #000; 
    }
    .showroom-card .overlay p { 
        margin: 0.25rem 0 0 0; 
        font-size: 1rem; 
    }
</style>
<?php
$extra_styles = ob_get_clean();
include 'includes/header.php';

$sql = "SELECT s.id, s.city, s.listing_id, s.image_url, COUNT(c.id) AS car_count 
        FROM showrooms s 
        LEFT JOIN cars c ON s.id = c.showroom_id 
        GROUP BY s.id 
        ORDER BY s.city, s.listing_id";
$showrooms_result = $conn->query($sql);
?>

<!-- START: Page-specific content -->
<div class="container">
    <h2 style="text-align: center;">Our Showrooms</h2>
    <p style="text-align: center; max-width: 600px; margin: -1.5rem auto 2.5rem auto;">
        Click on any showroom to see the high-quality, pre-owned cars currently available at that location.
    </p>
    
    <div class="showroom-grid">
        <?php if ($showrooms_result && $showrooms_result->num_rows > 0): ?>
            <?php while($showroom = $showrooms_result->fetch_assoc()): ?>
                <a href="cars_by_showroom.php?id=<?php echo $showroom['id']; ?>" class="showroom-card" title="View cars in <?php echo htmlspecialchars($showroom['city']); ?>">
                    <img class="showroom-card-bg-image" 
                         src="<?php echo htmlspecialchars($showroom['image_url'] ?: 'images/default-placeholder.png'); ?>" 
                         alt="Photo of the <?php echo htmlspecialchars($showroom['city']); ?> Showroom">
                    <div class="overlay">
                        <h3><?php echo htmlspecialchars($showroom['city']); ?></h3>
                        <p><?php echo $showroom['car_count']; ?> Cars Available</p>
                    </div>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="grid-column: 1 / -1; text-align: center;">No showrooms have been listed yet. Please check back soon!</p>
        <?php endif; ?>
    </div>
</div>
<!-- END: Page-specific content -->

<?php
include 'includes/footer.php'; 
?>