<?php $today = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Nightscout Data Transfer Tool</title>
        <link rel="icon" type="image/x-icon" href="/assets/nightscoutfavicon.jpg">
        <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="style.css">
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>

<?php
    //Sweet Alert if the API secret was entered incorrectly
    if(!empty($_GET['e'])) {
        ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'The API_SECRET you entered was incorect.'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/';
                }
            })
        </script>
        <?php
    }
        //Sweet Alert if the process was successful
        if(!empty($_GET['s'])) {
            ?>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Woohoo!',
                    text: 'Your nightscout data was transferred succesfuilly!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/';
                    }
                })
            </script>
            <?php
        }
?>

    <!-- Loading div whch gets toggled on page load or button click -->
    <div id="loading"></div>
        <script>
            function onReady(callback) {
            var intervalId = window.setInterval(function() {
                if (document.getElementsByTagName('body')[0] !== undefined) {
                window.clearInterval(intervalId);
                callback.call(this);
                }
            }, 1000);
            }

            function setVisible(selector, visible) {
            document.querySelector(selector).style.display = visible ? 'flex' : 'none';
            }

            onReady(function() {
            setVisible('.container', true);
            setVisible('#loading', false);
            });
       </script>
        <div class="container">

            <div class="form-container">
                <div class="logo">
                    <img src="/assets/nightscout.svg" alt="white nightscout logo" width="90px;">
                </div>
                <h1>Nightscout Data Transfer Tool</h1>

                <?php
                        if(!empty($_POST['oldUrl']) && !empty($_POST['newUrl']) && !empty($_POST['newApi'])) {
                            
                            //Sanitize the data and convert to lower case.
                            $oldUrl = strtolower(filter_var($_POST['oldUrl'], FILTER_SANITIZE_URL));
                            $newUrl = filter_var($_POST['newUrl'], FILTER_SANITIZE_URL);
                            $apiSecret = $_POST['newApi'];

                            if(!empty($_POST['oldApi'])) {
                                $oldApi = $_POST['oldApi'];
                            }
                            
                            $fromDate = $_POST['fromDate'];
                            $toDate = $_POST['toDate'];
                            //I'm not sure why, but we need to add on 1 day to the $toDate variable.
                            $toDate = strtotime($toDate);
                            $toDate = strtotime("+1 day", $toDate);
                            $toDate = date('Y-m-d', $toDate);

                            //Prepare the old URL so we can accept variations from the front end.
                            $oldExplode = explode('.', $oldUrl, 3);

                            if(str_contains($oldExplode[0], 'https://')) {
                                $oldSub = str_replace('https://', '', $oldExplode[0]);
                            } else if (str_contains($oldExplode[0], 'http://')) {
                                $oldSub = str_replace('http://', '', $oldExplode[0]);
                            } else {
                                $oldSub = $oldExplode[0];
                            }

                            $oldDom = $oldExplode[1];
                            $oldTld = str_replace('/', '', $oldExplode[2]);

                            $oldSecureDomain = 'https://' . $oldSub . '.' . $oldDom . '.' . $oldTld;
                            $oldInsecureDomain = 'http://' . $oldSub . '.' . $oldDom . '.' . $oldTld;


                            //Prepare the new URL so we can accept variations from the front end.
                            $newExplode = explode('.', $newUrl, 3);

                            if(str_contains($newExplode[0], 'https://')) {
                                $newSub = str_replace('https://', '', $newExplode[0]);
                            } else if (str_contains($newExplode[0], 'http://')) {
                                $newSub = str_replace('http://', '', $newExplode[0]);
                            } else {
                                $newSub = $newExplode[0];
                            }

                            $newDom = $newExplode[1];
                            $newTld = str_replace('/', '', $newExplode[2]);

                            $newSecureDomain = 'https://' . $newSub . '.' . $newDom . '.' . $newTld;
                            $newInsecureDomain = 'http://' . $newSub . '.' . $newDom . '.' . $newTld;

                            //Hash the API_SECRET so the new nightscout instance can accept the upload.
                            $hashedSecret = sha1($apiSecret);
                            $oldHash = sha1($oldApi);
                            
                            include $_SERVER['DOCUMENT_ROOT'] . '/transfer-profile.php';
                            include $_SERVER['DOCUMENT_ROOT'] . '/transfer-entries.php';
                            include $_SERVER['DOCUMENT_ROOT'] . '/transfer-treatments.php';
                        }
                ?>

                <form action="" method="post">
                    <div class="sub-group">
                        <h3>Date Range</h3>
                        <h4>(max. 6 months at once)</h4>
                        <div class="form-flex">
                            <div class="form-group">
                                <label for="fromDate">From</label>
                                <input type="date" name="fromDate">
                            </div>
                            <div class="form-group">
                                <label for="toDate">To</label>
                                <input type="date" name="toDate">
                            </div>
                        </div>
                    </div>
                    <div class="sub-group">
                        <h3>Old Nightscout</h3>
                        <div class="form-group">
                            <label for="oldUrl">Old Nightscout URL</label>
                            <input type="text" name="oldUrl">
                        </div>
                        <div class="form-group">
                            <label for="oldApi">Old Nightscout API_SECRET</label>
                            <div><p style="font-size: 12px;"><i>Only required if your old site had AUTH_DEFAULT_ROLES set to 'denied'.</i></p></div>
                            <input type="text" name="oldApi">
                        </div>
                    </div>
                    <div class="sub-group">
                        <h3>New Nightscout</h3>
                        <div class="form-group">
                            <label for="newUrl">New Nightscout URL</label>
                            <input type="text" name="newUrl">
                        </div>
                        <div class="form-group">
                            <label for="newApi">New Nightscout API_SECRET</label>
                            <input type="text" name="newApi">
                        </div>
                    </div>
                    <button type="submit" id="transfer-btn">TRANSFER DATA</button>
                </form>
            </div>
            
       </div>

       <!-- Toggle the loading GIF upon button click -->
       <script>
        let button = document.getElementById('transfer-btn');
        let loader = document.getElementById('loading');

        button.addEventListener('click', function() {
            loader.style.removeProperty('display');
        })
       </script>

    </body>
</html>
