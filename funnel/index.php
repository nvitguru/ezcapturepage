<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="EZ Capture Page">
    <meta name="description" content="EZ Capture Page Custom Marketing Funnel Design">
    <!-- ======== Page title ============ -->
    <title>Marketing Funnel - EZ Capture Page</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

</head>
<body>
<div class="superNav border-bottom py-2 bg-primary text-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 centerOnMobile">
                <span class="d-none d-lg-inline-block d-md-inline-block d-sm-inline-block d-xs-none me-3"><a href="mailto:support@ezcapturepage.com"><i class="fa fa-envelope"></i> <strong>support@ezcapturepage.com</strong></a></span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 d-none d-lg-block d-md-block-d-sm-block d-xs-none text-end">
                <strong>ORDER NOW - GET YOUR FUNNEL IN AS LITTLE AS 24 HOURS!</strong>
            </div>
        </div>
    </div>
</div>
<nav class="navbar navbar-expand-lg bg-white sticky-top navbar-light p-3 shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="#"><img src="../images/ez-capture-page-logo.png" class="img-fluid" style="max-width: 100px;"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class=" collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav ms-auto ">
                <li class="nav-item">
                    <a class="nav-link mx-2 text-uppercase" href="#home"><span><i class="fas fa-play"></i></span> Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link mx-2 text-uppercase" href="#features"><span><i class="fas fa-play"></i></span> Features</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link mx-2 text-uppercase" href="#pricing"><span><i class="fas fa-play"></i></span> Pricing</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link mx-2 text-uppercase" href="#faq"><span><i class="fas fa-play"></i></span> FAQ's</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link mx-2 text-uppercase" href="#testimonials"><span><i class="fas fa-play"></i></span> Testimonials</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto ">
                <li class="nav-item">
                    <a class="btn btn-outline-primary text-uppercase"  href="#startModal" data-bs-toggle="modal"><i class="fa-solid fa-rocket me-1"></i> Get Started</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<section id="opener">
    <div class="container full-screen">
        <div class="row full-screen">
            <?php include_once '../includes/alerts.php' ?>
            <div class="col-lg-7 my-auto opener-text">
                <h4>Double your lead acquisition & sales conversions</h4>
                <h1>Boost Profits with a <span>Sales Funnel</span> & <span>Automation</span></h1>
            </div>
            <div class="col-lg-5 my-auto">
                <img src="images/funnel-main-image.png" class="img-fluid">
            </div>
        </div>
    </div>
</section>

<section id="opener-stats">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 stats-border">
                <div class="row row-eq-height">
                    <div class="col-7 text-white stats-number my-auto text-end">
                        700
                    </div>
                    <div class="col-5 text-white stats-text my-auto">
                        FUNNELS CREATED
                    </div>
                </div>
            </div>
            <div class="col-lg-3 stats-border">
                <div class="row row-eq-height">
                    <div class="col-7 text-white stats-number my-auto text-end">
                        500
                    </div>
                    <div class="col-5 text-white stats-text my-auto">
                        SATISFIED CUSTOMERS
                    </div>
                </div>
            </div>
            <div class="col-lg-3 stats-border">
                <div class="row row-eq-height">
                    <div class="col-7 text-white stats-number my-auto text-end">
                        1.1m
                    </div>
                    <div class="col-5 text-white stats-text my-auto">
                        LEADS CAPTURED
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row row-eq-height">
                    <div class="col-7 text-white stats-number my-auto text-end">
                        1.5k
                    </div>
                    <div class="col-5 text-white stats-text my-auto">
                        CUPS OF COFFEE
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="features" class="section-padding-150">
    <div class="container">
        <div class="row row-eq-height">
            <div class="col-lg-5 text-center my-auto">
                <img src="images/funnel-feature-image.png" class="img-fluid">
            </div>
            <div class="col-lg-7 my-auto">
                <div class="row">
                    <div class="col-12 text-center">
                        <h1>Features Our Service Provide</h1>
                    </div>
                    <div class="col-12 text-center position-relative mb-5">
                        <hr>
                        <span class="divider"><img src="../images/ez-capture-page-icon.png" class="divider-img"></span>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <h4>Custom Lead Capture Page</h4>
                        <h5>Our funnels are tailor-made lead capture pages designed to engage prospects and boost conversions with personalized branding and messaging.</h5>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <h4>Mobile Optimized Design</h4>
                        <h5>Our funnels are responsive, mobile-friendly pages ensuring seamless user experience and engagement across all devices, boosting lead generation.</h5>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <h4>CRM Integrations</h4>
                        <h5>We effortlessly connect with popular CRM platforms to manage leads, track progress, and streamline your follow-up processes automatically.</h5>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <h4>Drip Email Campaign</h4>
                        <h5>We implement an automated, time-based email sequences to nurture leads, keep them engaged, and guide them through your marketing funnel.</h5>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-6 d-grid mt-4">
                        <a class="btn btn-outline-primary btn-lg text-uppercase"  href="#startModal" data-bs-toggle="modal"><i class="fa-solid fa-rocket me-1"></i> Get Started Today!</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="pricing" class="bg-gray section-padding-150">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1>Service Pricing Packages We Offer</h1>
            </div>
            <div class="col-12 text-center position-relative mb-5">
                <hr>
                <span class="divider"><img src="../images/ez-capture-page-icon.png" class="divider-img"></span>
            </div>
        </div>
        <div class="row row-eq-height justify-content-center">
            <div class="col-lg-4 my-auto">
                <div class="card shadow-lg">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 text-center mb-4">
                                <h1>$199</h1>
                                <h4>Funnel Creation</h4>
                            </div>
                            <div class="col-12 mb-4">
                                <table class="table">
                                    <tr>
                                        <td class="text-center">Customized Capture Page</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">Mobile Responsive Design</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">CRM Integration</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">Drip Email Campaign Setup</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">Server Deployment</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-12 d-grid">
                                <a class="btn btn-outline-primary btn-lg text-uppercase"  href="#startModal" data-bs-toggle="modal"><i class="fa-solid fa-rocket me-1"></i> Get Started Today!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 my-auto">
                <div class="card shadow-lg">
                    <div class="ribbon red"><span>Popular</span></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 text-center mb-4">
                                <h1>$199 <span>+ $4.99/mo</span></h1>
                                <h4>Funnel Creation<br>(+ Hosting & SSL Certificate)</h4>
                            </div>
                            <div class="col-12 mb-4">
                                <table class="table">
                                    <tr>
                                        <td class="text-center">Customized Capture Page</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">Mobile Responsive Design</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">CRM Integration</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">Drip Email Campaign Setup</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">Monitored Hosting</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">Standard SSL Certificate</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">Technical Support</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-12 d-grid">
                                <a class="btn btn-outline-primary btn-lg text-uppercase"  href="#startModal" data-bs-toggle="modal"><i class="fa-solid fa-rocket me-1"></i> Get Started Today!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="faq" class="section-padding-150">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="row justify-content-center">
                    <div class="col-lg-9 text-center">
                        <h1>Frequently Asked Questions</h1>
                    </div>
                    <div class="col-lg-9 text-center position-relative mb-5">
                        <hr>
                        <span class="divider"><img src="../images/ez-capture-page-icon.png" class="divider-img"></span>
                    </div>
                </div>
                <div class="accordion accordion-flush" id="accordionFAQ">
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <h3>General Questions</h3>
                                </div>
                                <div class="col-12">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="flush-headingOne">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                                What is a network marketing funnel, and how does it work?
                                            </button>
                                        </h2>
                                        <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFAQ">
                                            <div class="accordion-body">A network marketing funnel guides potential leads through a series of steps designed to engage, educate, and convert them into customers or team members. It automates the process, from capturing leads to following up with targeted emails.</div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="flush-headingTwo">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                                                How customizable are the lead capture pages?
                                            </button>
                                        </h2>
                                        <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFAQ">
                                            <div class="accordion-body">Our lead capture pages are fully customizable, allowing you to modify layouts, colors, text, and images to reflect your brand and resonate with your target audience for maximum engagement.</div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="flush-headingThree">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                                                Can I integrate my existing CRM with your funnel system?
                                            </button>
                                        </h2>
                                        <div id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="flush-headingThree" data-bs-parent="#accordionFAQ">
                                            <div class="accordion-body">Yes, we offer seamless CRM integrations with popular platforms, enabling you to track and manage leads effortlessly, ensuring no opportunity is missed.</div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="flush-headingFour">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFour" aria-expanded="false" aria-controls="flush-collapseFour">
                                                Is the platform mobile-friendly?
                                            </button>
                                        </h2>
                                        <div id="flush-collapseFour" class="accordion-collapse collapse" aria-labelledby="flush-headingFour" data-bs-parent="#accordionFAQ">
                                            <div class="accordion-body">Absolutely! All our funnel pages are mobile-optimized, ensuring a smooth and responsive user experience across all devices, helping you capture leads on-the-go.</div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="flush-headingFive">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFive" aria-expanded="false" aria-controls="flush-collapseFive">
                                                What is a drip email campaign, and how does it help my business?
                                            </button>
                                        </h2>
                                        <div id="flush-collapseFive" class="accordion-collapse collapse" aria-labelledby="flush-headingFive" data-bs-parent="#accordionFAQ">
                                            <div class="accordion-body">A drip email campaign is an automated series of pre-written emails sent at specific intervals to nurture leads. It helps keep prospects engaged, building trust and encouraging conversions over time.</div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="flush-headingSix">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseSix" aria-expanded="false" aria-controls="flush-collapseSix">
                                                Do I need technical knowledge to set up my funnel?
                                            </button>
                                        </h2>
                                        <div id="flush-collapseSix" class="accordion-collapse collapse" aria-labelledby="flush-headingSix" data-bs-parent="#accordionFAQ">
                                            <div class="accordion-body">No technical knowledge is required! We create and setup everything for you so all you have to do is start driving traffic to your page once we're done.</div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="flush-headingSeven">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseSeven" aria-expanded="false" aria-controls="flush-collapseSeven">
                                                Is support available if I need help setting up my funnel?
                                            </button>
                                        </h2>
                                        <div id="flush-collapseSeven" class="accordion-collapse collapse" aria-labelledby="flush-headingSeven" data-bs-parent="#accordionFAQ">
                                            <div class="accordion-body">For those who chose to use our hosting service we do provide customer support for the life of the page. If you chose to host the page yourself, we offer 7 days of support once we have deployed the page to your hosting server.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <h3>Service Questions</h3>
                                </div>
                                <div class="col-12">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="flush-headingEight">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseEight" aria-expanded="false" aria-controls="flush-collapseEight">
                                                What do I need to get started?
                                            </button>
                                        </h2>
                                        <div id="flush-collapseEight" class="accordion-collapse collapse" aria-labelledby="flush-headingEight" data-bs-parent="#accordionFAQ">
                                            <div class="accordion-body">
                                                <p class="mb-1">To get started, all customers will need the minimum:</p>
                                                <ul class="mb-3">
                                                    <li>Domain Name</li>
                                                    <li>CRM System</li>
                                                </ul>
                                                <p class="mb-1">If you choose to host the funnel, you will also need:</p>
                                                <ul>
                                                    <li>Hosting server with cPanel</li>
                                                    <li>Standard SSL Certificate</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="flush-headingNine">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseNine" aria-expanded="false" aria-controls="flush-collapseNine">
                                                What is a domain name and where can I get one?
                                            </button>
                                        </h2>
                                        <div id="flush-collapseNine" class="accordion-collapse collapse" aria-labelledby="flush-headingNine" data-bs-parent="#accordionFAQ">
                                            <div class="accordion-body">
                                                <p>A domain name is the unique address used to access a website on the internet. It typically consists of a name (e.g., example) and an extension (e.g., .com) and helps users easily find and identify websites.</p>
                                                <p class="mb-1">While there are many domain providers out there to choose from, some providers we recommend are:</p>
                                                <ul>
                                                    <li><a href="https://bluehost.com" target="_blank">Bluehost.com</a></li>
                                                    <li><a href="https://godaddy.com" target="_blank">GoDaddy.com</a></li>
                                                    <li><a href="https://hostgator.com" target="_blank">HostGator.com</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="flush-headingTen">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTen" aria-expanded="false" aria-controls="flush-collapseTen">
                                                What is a CRM Service which do you recommend?
                                            </button>
                                        </h2>
                                        <div id="flush-collapseTen" class="accordion-collapse collapse" aria-labelledby="flush-headingTen" data-bs-parent="#accordionFAQ">
                                            <div class="accordion-body">
                                                <p>A CRM system (Customer Relationship Management) is software that helps businesses manage and analyze interactions with customers and prospects. It centralizes customer data, streamlines communication, and enhances relationships, ultimately improving sales, service, and retention efforts.</p>
                                                <p class="mb-1">There are quite a few CRM systems on the market, but we recommend the following for beginners:</p>
                                                <ul>
                                                    <li><a href="https://aweber.com" target="_blank">Aweber.com</a></li>
                                                    <li><a href="https://getresponse.com" target="_blank">GetResponse.com</a></li>
                                                    <li><a href="https://mailchimp.com" target="_blank">MailChimp.com</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="flush-headingEleven">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseEleven" aria-expanded="false" aria-controls="flush-collapseEleven">
                                                What is a Hosting Service which do you recommend?
                                            </button>
                                        </h2>
                                        <div id="flush-collapseEleven" class="accordion-collapse collapse" aria-labelledby="flush-headingEleven" data-bs-parent="#accordionFAQ">
                                            <div class="accordion-body">
                                                <p>A hosting service provides the technology and resources needed to store a website's files and make it accessible on the internet. It allows websites to be hosted on servers, ensuring they are available to users 24/7.</p>
                                                <p class="mb-1">While there are many different hosting services on the market, but we recommend the following for ease of use:</p>
                                                <ul>
                                                    <li><a href="https://bluehost.com" target="_blank">Bluehost.com</a></li>
                                                    <li><a href="https://godaddy.com" target="_blank">GoDaddy.com</a></li>
                                                    <li><a href="https://hostgator.com" target="_blank">HostGator.com</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="flush-headingTwelve">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwelve" aria-expanded="false" aria-controls="flush-collapseTwelve">
                                                What is a SSL Certificate and how do I get one?
                                            </button>
                                        </h2>
                                        <div id="flush-collapseTwelve" class="accordion-collapse collapse" aria-labelledby="flush-headingTwelve" data-bs-parent="#accordionFAQ">
                                            <div class="accordion-body">
                                                <p>A hosting SSL certificate is a digital certificate that encrypts data between a website and its visitors, ensuring secure communication. It provides authentication, data integrity, and builds trust by displaying "https" and a padlock symbol in the browser's address bar.</p>
                                                <p>Usually the hosting provider will have the SSL certificate to purchase along with the hosting service. Which ever hosting provider you use should help setting you up with an SSL certificate.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="flush-headingThirteen">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThirteen" aria-expanded="false" aria-controls="flush-collapseThirteen">
                                                Why should I host the funnel system through you?
                                            </button>
                                        </h2>
                                        <div id="flush-collapseThirteen" class="accordion-collapse collapse" aria-labelledby="flush-headingThirteen" data-bs-parent="#accordionFAQ">
                                            <div class="accordion-body">
                                                <p class="mb-1">Short answer... to save money! Hosting accounts and SSL Certificates can get costly. We offer a complete hosting package with our service for a fraction of the price. Below is a breakdown of the cost associated:</p>
                                                <ul>
                                                    <li>Hosting service: $9.99 - $14.99 per month</li>
                                                    <li>SSL Certificate: $99 per year</li>
                                                </ul>
                                                <p>With our service, you get the hosting service as well as the SSL Certificate for only <strong>$4.99 per month</strong>! Not only do you get the cost savings, but also don't have to worry about the hassle of setting everything up.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-5 d-grid mt-4">
                <a class="btn btn-outline-primary btn-lg text-uppercase" href="#startModal" data-bs-toggle="modal"><i class="fa-solid fa-rocket me-1"></i> Get Started Today!</a>
            </div>
        </div>
    </div>
</section>

<section id="call-to-action">
    <div class="container">
        <div class="row">
            <div class="col-12 text-white cta-text text-center">
                GET STARTED TODAY - HAVE YOUR FUNNEL IN 24 HOURS!
            </div>
        </div>
    </div>
</section>

<section id="testimonials" class="section-padding-150">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="row justify-content-center">
                    <div class="col-lg-9 text-center">
                        <h1>What Our Clients Are Saying</h1>
                    </div>
                    <div class="col-lg-9 text-center position-relative mb-5">
                        <hr>
                        <span class="divider"><img src="../images/ez-capture-page-icon.png" class="divider-img"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 mb-4">
                        <div class="card shadow-lg">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <h5>"This funnel system transformed my network marketing business! The custom lead capture page and seamless CRM integrations helped me manage leads effortlessly. I’m seeing more conversions and growth than ever before."</h5>
                                    </div>
                                    <div class="col-12 my-2">
                                        <hr>
                                    </div>
                                    <div class="col-12 ps-4">
                                        <ul class="list-unstyled list-inline">
                                            <li class="list-inline-item gold"><i class="fas fa-star"></i></li>
                                            <li class="list-inline-item gold"><i class="fas fa-star"></i></li>
                                            <li class="list-inline-item gold"><i class="fas fa-star"></i></li>
                                            <li class="list-inline-item gold"><i class="fas fa-star"></i></li>
                                            <li class="list-inline-item gold"><i class="fas fa-star"></i></li>
                                        </ul>
                                    </div>
                                    <div class="col-12 ps-4">
                                        <h5 class="mb-1">Brandon McKinnley</h5>
                                        <p class="mb-0">Portland, OR</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-4">
                        <div class="card shadow-lg">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <h5>"I love how easy this platform makes managing my marketing efforts. The drip email campaigns keep my leads engaged, and the mobile-optimized design ensures everything looks perfect on any device. It’s a game-changer!"</h5>
                                    </div>
                                    <div class="col-12 my-2">
                                        <hr>
                                    </div>
                                    <div class="col-12 ps-4">
                                        <ul class="list-unstyled list-inline">
                                            <li class="list-inline-item gold"><i class="fas fa-star"></i></li>
                                            <li class="list-inline-item gold"><i class="fas fa-star"></i></li>
                                            <li class="list-inline-item gold"><i class="fas fa-star"></i></li>
                                            <li class="list-inline-item gold"><i class="fas fa-star"></i></li>
                                            <li class="list-inline-item gold"><i class="fas fa-star"></i></li>
                                        </ul>
                                    </div>
                                    <div class="col-12 ps-4">
                                        <h5 class="mb-1">Jennifer Hinkle</h5>
                                        <p class="mb-0">Las Vegas, NV</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 mb-4">
                        <div class="card shadow-lg">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <h5>"Since switching to this funnel solution, I’ve seen a noticeable increase in both leads and sales. The customizable tools and automated features save me time while delivering fantastic results. I highly recommend it to any network marketer!"</h5>
                                    </div>
                                    <div class="col-12 my-2">
                                        <hr>
                                    </div>
                                    <div class="col-12 ps-4">
                                        <ul class="list-unstyled list-inline">
                                            <li class="list-inline-item gold"><i class="fas fa-star"></i></li>
                                            <li class="list-inline-item gold"><i class="fas fa-star"></i></li>
                                            <li class="list-inline-item gold"><i class="fas fa-star"></i></li>
                                            <li class="list-inline-item gold"><i class="fas fa-star"></i></li>
                                            <li class="list-inline-item gold"><i class="fas fa-star"></i></li>
                                        </ul>
                                    </div>
                                    <div class="col-12 ps-4">
                                        <h5 class="mb-1">Gregory Drake</h5>
                                        <p class="mb-0">Houston, TX</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="bg-gray text-center text-lg-start">
    <!-- Grid container -->
    <div class="container p-4">
        <!--Grid row-->
        <div class="row mt-4">
            <!--Grid column-->
            <div class="col-lg-4 col-md-12 mb-4 mb-md-0">
                <h5 class="text-uppercase mb-4">About company</h5>
                <p>Our mission is to empower network marketers with innovative, user-friendly funnel solutions that drive lead generation, maximize conversions, and streamline business growth. We provide personalized tools, seamless integrations, and automated marketing systems to fuel success and foster sustainable relationships.</p>
            </div>
            <!--Grid column-->

            <!--Grid column-->
            <div class="col-lg-4 col-md-6 mb-4 mb-md-0 ps-md-5">
                <h5 class="text-uppercase mb-4 pb-1">Quick Links</h5>

                <ul class="fa-ul" style="margin-left: 1.65em;">
                    <li class="mb-3">
                        <a href="#"><span class="fa-li"><i class="fas fa-play"></i></span >Terms of Service</a>
                    </li>
                    <li class="mb-3">
                        <a href="#"><span class="fa-li"><i class="fas fa-play"></i></span> Privacy Policy</a>
                    </li>
                    <li class="mb-3">
                        <a href="#"><span class="fa-li"><i class="fas fa-play"></i></span> Refund Policy</a>
                    </li>
                </ul>
            </div>
            <!--Grid column-->

            <!--Grid column-->
            <div class="col-lg-4 col-md-6 mb-4 mb-md-0 text-center">
                <h5 class="text-uppercase mb-4">Business hours</h5>

                <table class="table text-center text-white">
                    <tbody class="font-weight-normal">
                    <tr>
                        <td>Monday - Friday:</td>
                        <td>8am - 9pm</td>
                    </tr>
                    <tr>
                        <td>Saturday:</td>
                        <td>8am - 12pm</td>
                    </tr>
                    <tr>
                        <td>Sunday:</td>
                        <td>CLOSED</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <!--Grid column-->
        </div>
        <!--Grid row-->
    </div>
    <!-- Grid container -->

    <!-- Copyright -->
    <div class="bg-primary text-white text-center p-3">
        © 2024
        <a class="text-white" href="https://ezcapturepage.com">EZ Capture Page</a> - All Rights Reserved
    </div>
    <!-- Copyright -->
</footer>

<!-- Modal -->
<div class="modal fade" id="startModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 text-end mb-3">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="col-12 mb-3">
                        <h3>To get started, we'll need a little bit of information...</h3>
                    </div>
                </div>
                <form method="post" action="submitRequest.php">
                    <div class="row">
                        <div class="col-12 mb-4 form-group">
                            <label>Full Name</label>
                            <input type="text" class="form-control form-control-lg" name="name" placeholder="Johnathon Doe" required>
                        </div>
                        <div class="col-12 mb-4 form-group">
                            <label>Email Address</label>
                            <input type="email" class="form-control form-control-lg" name="email" placeholder="myeamail@emailaddress.com" required>
                        </div>
                        <div class="col-12 mb-4 form-group">
                            <label>Contact Phone</label>
                            <input type="tel" class="form-control form-control-lg" name="phone" placeholder="(555) 123-4567">
                        </div>
                        <div class="col-12 mb-4 form-group">
                            <label>Hosting Type</label>
                            <select class="form-select form-select-lg" name="hosting">
                                <option value="Yes! I will need hosting.">Yes! I will need hosting</option>
                                <option value="No, thank you. I will host it myself.">No, thank you. I will host it myself</option>
                            </select>
                        </div>
                        <div class="col-12 mb-4 form-group">
                            <label>Message</label>
                            <textarea class="form-control form-control-lg" rows="5" name="message"></textarea>
                        </div>
                        <div class="col-12 mb-4 form-group">
                            <label>Are you human?</label>
                            <input type="text" class="form-control form-control-lg" name="human" placeholder="9 + 3 =">
                        </div>
                        <div class="col-12 my-3 d-grid">
                            <button type="submit" class="btn btn-lg btn-outline-primary" name="submitRequest">SUBMIT REQUEST</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/all.min.js"></script>
</body>
</html>