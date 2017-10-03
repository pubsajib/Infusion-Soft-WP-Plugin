<?php 
/**
* Initial activate and deactivate class
*/
class tn_activate_deactivate extends tn_db {
    function __construct(){
        parent::__construct();
        // $this->create_tag_table();
    }

    // Create required tables
    public function createTables(){
        $this->create_settings_table();
        $this->create_token_table();
        $this->create_teacher_table();
        $this->create_address_table();
        $this->create_image_table();
        $this->create_campaign_table();

        $this->create_social_type_table();
        $this->insert_default_data();
        $this->create_contact_Social_table();

        $this->create_tag_table();
        $this->create_cat_table();
        $this->create_contact_tag_table();
    }

    // Delete all tables from database
    public function deleteTables(){
        $this->delete_settings_table();
        $this->delete_token_table();
        $this->delete_teacher_table();
        $this->delete_address_table();
        $this->delete_image_table();
        $this->delete_campaign_table();

        $this->delete_social_type_table();
        $this->delete_contact_Social_table();

        $this->delete_tag_table();
        $this->delete_cat_table();
        $this->delete_contact_tag_table();
    }
    public function create_contact_tag_table(){
        $charset_collate = $this->charset;
        $tableName = $this->contactTagTable;
        $sql = "CREATE TABLE IF NOT EXISTS `$tableName` (
                `contact_tags_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `contact_id` int(11) DEFAULT NULL,
                `tag_id` int(11) DEFAULT NULL,
                `create_date` TIMESTAMP NOT NULL,
                `update_date` TIMESTAMP NOT NULL
                ) $charset_collate;";
        $result = $this->db->query( $sql );
        return $result;
    }
    public function create_cat_table(){
        $charset_collate = $this->charset;
        $tableName = $this->tagCatTable;
        $sql = "CREATE TABLE IF NOT EXISTS `$tableName` (
                `category_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `category_name` varchar(128) DEFAULT NULL,
                `create_date` TIMESTAMP NOT NULL,
                `update_date` TIMESTAMP NOT NULL
                ) $charset_collate;";
        $result = $this->db->query( $sql );
        if ( $result ) {
            if ($this->insert_cats()) { return $result; }
        }
        return $result;
    }
    public function insert_cats(){
        $tableName = $this->tagCatTable;
        $sql = " INSERT INTO `".$tableName."` (`category_name`) VALUES ('hours'), ('speciality');";
        $result = $this->db->query( $sql );
        return $result;
    }
    public function create_tag_table(){
        $charset_collate = $this->charset;
        $tableName = $this->tagTable;
        $sql = "CREATE TABLE IF NOT EXISTS `$tableName` (
                `tag_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `category_id` int(11) DEFAULT NULL,
                `tag_name` varchar(256) DEFAULT NULL,
                `icon` varchar(128) DEFAULT NULL,
                `bi_line` text DEFAULT NULL,
                `create_date` TIMESTAMP NOT NULL,
                `update_date` TIMESTAMP NOT NULL
                ) $charset_collate;";
        $result = $this->db->query( $sql );
        if ( $result ) {
            if ($this->insert_tags()) { return $result; }
        }
        return false;
    }
    public function insert_tags(){
        $tableName = $this->tagTable;
        $sql = "INSERT INTO `".$tableName."` (`tag_id`, `category_id`, `tag_name`, `icon`, `bi_line`) VALUES
            (423, 1, '200HR Complete', 'tag-423.png', 'Certified 200-hour yoga teacher'),
            (145, 1, '500HR', 'tag-145.png', '500-hour training in progress'),
            (303, 1, '500HR complete', 'tag-303.png', 'Certified 500-hour yoga teacher'),
            (509, 1, '1000HRApproved or 1000HR (need to delete approved tag but haven''t done this yet bc it will alter the applications campaign)', 'tag-509.png', '1000-hour training in progress'),
            (143, 1, '1000HRApproved or 1000HR (need to delete approved tag but haven''t done this yet bc it will alter the applications campaign)', 'tag-143.png', '1000-hour training in progress'),
            (305, 1, '1000HR Complete', 'tag-305.png', 'Yoga Medicine certified 500-hour &1000-hour yoga teacher'),
            (178, 2, 'Hip', 'YogaMedicine-Icons_HIP 01-tag178.jpg', 'For hip and sacrum discomfort, see a teacher trained on the hip''s biomechanics'),
            (1088, 2, 'Hip 2', 'YogaMedicine-Icons_HIP 02-tag1088.jpg', 'This teacher has undergone further training in the hip''s biomechanics and dysfunctions to create individualized yoga plans'),
            (194, 2, 'Spine', 'YogaMedicine-Icons_SPINE 01-tag194.jpg', 'For back, neck or core issues, see a provide trained on the spine''s biomechanics.'),
            (1090, 2, 'Spine 2', 'YogaMedicine-Icons_SPINE 02-tag1090.jpg', 'This teacher has undergone further training in spinal biomechanics and dysfunctions to create individualized yoga plans'),
            (220, 2, 'Shoulder', 'YogaMedicine-Icons_SHOULDER 01-tag220.jpg', 'This teacher is trained in the biomechanics, range of motion and yoga options for common shoulder dysfunctions.'),
            (1092, 2, 'Shoulder 2', 'YogaMedicine-Icons_SHOULDER 02-tag1092.jpg', 'This teacher has undergone further training in the shoulder''s biomechanics and dysfunctions to create individualized yoga plans'),
            (1094, 2, 'Extremities', 'YogaMedicine-Icons_EXTREMITIES-tag1094.jpg', 'If you have a problem in an extremity, make sure your teacher is trained in the connecting area (foot to hip OR arm to shoulder). If you have shooting pain or numbness/tingling in the extremity, we recommend a teacher who is also trained in spine.'),
            (1096, 2, 'Full Body', 'YogaMedicine-Icons_FULL BODY-tag1096.jpg', 'This teacher has taken the core orthopedic modules (spine, shoulder, hip) in addition to this extra training to engage the full picture'),
            (1098, 2, '', 'YogaMedicine-Icons_EVALUATION-tag1098.jpg', 'This teacher has taken extra training to fine tune their assessment & application skills to deliver you a well designed yoga plan.'),
            (1100, 2, 'Internal 1 Nervous Sys & Restorative 60h', 'YogaMedicine-Icons_NERVOUS SYSTEM-13-tag1100.jpg', 'This teacher is trained to shape the practice specifically to address nervous system dysfunction, how to use restorative yoga and most importantly the relevance of this system to many different pathologies and how to utilize these concepts within the yoga practice.'),
            (1102, 2, 'Internal 2: Cardiovascular Respiratory & Pranayama 60h', 'YogaMedicine-Icons_CARDIOVASCULAR-RESPIRATORY-tag1102.jpg', 'This teachers is trained on how to shape a yoga practice specifically to address issues in cardiovascular or respiratory systems, & the therapeutic application of pranayama'),
            (1104, 2, 'Internal 3 Women''s Health 25h', 'YogaMedicine-Icons_WOMENS HEALTH & FERTILITY-tag1104.jpg', 'This teachers is trained on how to shape a yoga practice catered to the female reproductive cycle from a Western & Chinese Medicine perspective to take a highly-individualized approach.'),
            (1106, 2, 'Internal 4: Digestion Elimination & Detox (25hrs)', 'YogaMedicine-Icons_DIGESTIVE-tag1106.jpg', 'This teacher is trained to utilize our yoga practice to regulate the digestion & elimination and how & when to implement detoxification protocols'),
            (1120, 2, 'Internal 5: Cancer & Chronic Fatigue 25h', 'YogaMedicine-Icons_CANCER SUPPORT-tag1120.jpg', 'This teacher knows how a yoga practice can influence chronic fatigue &/or cancer, and how yoga can support you through the process.'),
            (1122, 2, 'Internal 6: Immune System & Autoimmune Disease 25h', 'YogaMedicine-Icons_AUTOIMMUNE SUPPORT-tag1122.jpg', 'This teachers possesses a big picture view of the function of the immune system, common dysfunction here and how to use a yoga practice to support and enhance immune function'),
            (1124, 2, 'Internal 7: Chronic Pain 25h', 'YogaMedicine-Icons_CHRONIC PAIN-tag1124.jpg', 'This training gives yoga teachers different tools and approaches to working with chronic, non-mechanical pain and yoga techniques that can be helpful when working with pain in any form'),
            (184, 2, 'CMMR', 'YogaMedicine-Icons_MYOFACIAL & CHINESE MEDICINE-tag184.jpg', 'This trainging featured a split focus on TCM & MFR with an investigation of Chinese Medicine''s yin/yang theory, 5 elements & individual constitutions alongside therapeutic techniques for myofascial release'),
            (1108, 2, 'Chinese Med', 'YogaMedicine-Icons_CHINESE MEDICINE-tag1108.jpg', 'The TCM training lays a foundation in Chinese Medicine''s yin/yang theory, 5 elements & individual constitutions as it relates to yoga'),
            (1110, 2, 'Myofascial Release', '', 'Looking for new techniques to reduce tension & restricted range of motion? Find a teacher with MFR training for a therapeutic application of fascial release techniques to address fascial restrictions and trigger points.'),
            (202, 2, 'AdjustAssisting', 'YogaMedicine-Icons_Assisting&Adjusting-Tag202.png', 'This teacher is trained for safe and intelligent hands-on adjustments of yoga postures'),
            (463, 2, 'Yin Meditation', 'YogaMedicine-Icons_YIN & MEDITATION-tag463.jpg', 'Pair with a teacher trained in contemplative practices who is familiar with the science behind how yin/meditation can be used more therapeutically.'),
            (198, 2, 'Sequencing', 'YogaMedicine-Icons_SequencingwithPurpose2-tag198.png', 'This training gives depth for intelligent and progressive sequencing designed around a clear and grounded purpose'),
            (1118, 2, 'Teaching voice 60h', 'YogaMedicine-Icons_FINDING YOUR VOICE-tag1118.jpg', 'This training investigates and develops the tools teachers employ to convey a message through practice and their voice'),
            (1116, 2, 'Capstone 40-60h', 'YogaMedicine-Icons_1000 HR CAPSTONE-tag1116.jpg', 'This is a more intimate module for those completing their 1000hour program and involves case studies and working one-on-one with Tiffany looking at special cases and deepening your work with private clients.'),
            (465, 2, 'Seva 15-50h', 'YogaMedicine-Icons_SEVA-tag465.jpg', ''),
            (214, 2, 'Athletes IM', 'YogaMedicine-Icons_YOGA FOR ATHLETES-tag214.jpg', 'This training helps teachers create effective yoga classes for athletes with injuries, injury prevention, performance enhancement & mental preparation.'),
            (756, 2, 'Chinese Med IM', '', 'Looking for new techniques to reduce tension & restricted range of motion? Find a teacher with MFR training for a therapeutic application of fascial release techniques to address fascial restrictions and trigger points. This is a shorter version of the extended 60hour module that we offer.'),
            (876, 2, 'Cadaver Lab IM', 'YogaMedicine-Icons_DISSECTION LAB-tag876.jpg', 'This immersion offers a rare opportunity for participants to broaden their anatomical knowledge through the exploration of a real human form in a yoga anatomist context.'),
            (206, 2, 'Detox IM', 'YogaMedicine-Icons_DETOX IMMERSION-tag206.jpg', 'This training is a personal exploration of detoxing to optimize your health in order to share these simple yet insightful tools with your students.'),
            (461, 2, 'Hip IM', 'YogaMedicine-Icons_HIP 02-tag461.jpg', 'For hip and sacrum discomfort, see a teacher trained on the hip''s biomechanics. This is a shorter version of the extended 60hour module that we offer.'),
            (1062, 2, 'Myofascial IM', 'YogaMedicine-Icons_MYOFACIAL RELEASE-tag1062.jpg', 'Looking for new techniques to reduce tension & restricted range of motion? Find a teacher with MFR training for a therapeutic application of fascial release techniques to address fascial restrictions and trigger points. This is a shorter version of the extended 60hour module that we offer.'),
            (708, 2, 'Research IM', 'YogaMedicine-Icons_RESEARCH-tag708.jpg', 'This training focuses on conducting yoga-based research with human participants. It addresses the specifics of a yoga asana &/or mindfulness protocol designed by Yoga Medicine Research Institute’s ongoing research projects.'),
            (467, 2, 'Sequencing IM', 'YogaMedicine-Icons_SEQUENCING WITH PURPOSE-tag467.jpg', 'This training gives depth for intelligent and progressive sequencing designed around a clear and grounded purpose. This is a shorter version of the extended 60hour module that we offer.'),
            (317, 2, 'Shoulder IM', 'YogaMedicine-Icons_SHOULDER 02-tag317.jpg', 'This teacher is trained in the biomechanics, range of motion and yoga options for common shoulder dysfunctions. This is a shorter version of the extended 60hour module that we offer.'),
            (459, 2, 'Spine IM', 'YogaMedicine-Icons_SPINE 02-tag459.jpg', 'For back, neck or core issues, see a provide trained on the spine''s biomechanics. This is a shorter version of the extended 60hour module that we offer.');";
        $result = $this->db->query( $sql );
        return $result;
    }
    public function delete_tag_table(){
        $tableName = $this->tagTable;
        $sql = "DROP TABLE IF EXISTS `$tableName`;";
        if ( $this->db->query($sql) ) { return true; }
        else { return false; }
    }
    public function delete_cat_table(){
        $tableName = $this->tagCatTable;
        $sql = "DROP TABLE IF EXISTS `$tableName`;";
        if ( $this->db->query($sql) ) { return true; }
        else { return false; }
    }
    public function delete_contact_tag_table(){
        $tableName = $this->contactTagTable;
        $sql = "DROP TABLE IF EXISTS `$tableName`;";
        if ( $this->db->query($sql) ) { return true; }
        else { return false; }
    }
    
    public function create_social_type_table() {
        $charset_collate = $this->charset;
        $tableName = $this->socialTypeTable;
        $sql = "CREATE TABLE IF NOT EXISTS `$tableName` (
                `social_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `social_name` varchar(128) DEFAULT NULL,
                `create_date` TIMESTAMP NOT NULL,
                `update_date` TIMESTAMP NOT NULL
                ) $charset_collate;";
        $result = $this->db->query( $sql );
        return $result;
    }
    public function insert_default_data(){
        $tableName = $this->socialTypeTable;
        $sql = "";
        $sql .= "INSERT INTO `$tableName` (`social_name`, `create_date`, `update_date`) VALUES ";
        $sql .= "('Facebook', NOW(), NOW()), ";
        //$sql .= "('Twitter', NOW(), NOW()), ";
        $sql .= "('LinkedIn', NOW(), NOW()), ";
        // $sql .= "('Google+', NOW(), NOW()), ";
        // $sql .= "('YouTube', NOW(), NOW()), ";
         //$sql .= "('Pinterest', NOW(), NOW()), ";
         $sql .= "('Instagram', NOW(), NOW()); ";
        // $sql .= "('Tumblr', NOW(), NOW());";
        $result = $this->db->query( $sql );
        return $result;
    }
    public function delete_social_type_table(){
        $tableName = $this->socialTypeTable;
        $sql = "DROP TABLE IF EXISTS `$tableName`;";
        if ( $this->db->query($sql) ) { return true; }
        else { return false; }
    }
    public function create_contact_Social_table() {
        $charset_collate = $this->charset;
        $tableName = $this->contactSocialTable;
        $sql = "CREATE TABLE IF NOT EXISTS `$tableName` (
                `contact_social_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `social_crm_id` int(11) DEFAULT NULL,
                `contact_id` int(11) DEFAULT NULL,
                `social_id` int(11) DEFAULT NULL,
                `account_name` varchar(200) DEFAULT NULL,
                `create_date` TIMESTAMP NOT NULL,
                `update_date` TIMESTAMP NOT NULL
                ) $charset_collate;";
        $result = $this->db->query( $sql );
        return $result;
    }
    public function delete_contact_Social_table(){
        $tableName = $this->contactSocialTable;
        $sql = "DROP TABLE IF EXISTS `$tableName`;";
        if ( $this->db->query($sql) ) { return true; }
        else { return false; }
    }

    public function create_campaign_table() {
        $charset_collate = $this->charset;
        $tableName = $this->campaignTable;
        $sql = "CREATE TABLE IF NOT EXISTS `$tableName` (
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `key` varchar(256) DEFAULT NULL,
                `value` text
                ) $charset_collate;";
        $result = $this->db->query( $sql );
        if ( $result ) { $this->insert_campaign_table(); }
        return $result;
    }
    public function insert_campaign_table(){
        $tableName = $this->campaignTable;
        $sql = "";
        $sql .= "INSERT INTO `$tableName` (`key`, `value`) VALUES ";
        $sql .= "('autolog_status', 'true'), ";
        $sql .= "('admin_approval_status', 'true'); ";
        $result = $this->db->query( $sql );
        return $result;
    }
    public function delete_campaign_table(){
        $tableName = $this->campaignTable;
        $sql = "DROP TABLE IF EXISTS `$tableName`;";
        if ( $this->db->query($sql) ) { return true; }
        else { return false; }
    }
    public function create_image_table() {
        $charset_collate = $this->charset;
        $tableName = $this->imageTable;
        $sql = "CREATE TABLE IF NOT EXISTS `$tableName` (
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `url` varchar(128) DEFAULT NULL,
                `contact_id` varchar(32) DEFAULT NULL,
                `create_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                ) $charset_collate;";
        $result = $this->db->query( $sql );
        return $result;
    }
    public function delete_image_table(){
        $tableName = $this->imageTable;
        $sql = "DROP TABLE IF EXISTS `$tableName`;";
        if ( $this->db->query($sql) ) { return true; }
        else { return false; }
    }
    public function create_settings_table(){
        $charset_collate = $this->charset;
        $tableName = $this->settingTable;
        $sql = "CREATE TABLE IF NOT EXISTS `$tableName` (
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `client_id` varchar(32) DEFAULT NULL,
                `client_secret` varchar(32) DEFAULT NULL,
                `create_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                ) $charset_collate;";
        $result = $this->db->query( $sql );
        return $result;
    }

    public function delete_settings_table(){
        $tableName = $this->settingTable;
        $sql = "DROP TABLE IF EXISTS `$tableName`;";
        if ( $this->db->query($sql) ) { return true; }
        else { return false; }
    }

    public function create_token_table(){
        $charset_collate = $this->charset;
        $tableName = $this->tokenTable;
        $sql = "CREATE TABLE IF NOT EXISTS $tableName (
          `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `access_token` varchar(30) DEFAULT NULL,
          `refresh_token` varchar(30) DEFAULT NULL,
          `end_of_life` varchar(128) DEFAULT NULL,
          `scope` varchar(128) DEFAULT NULL,
          `create_date` datetime NOT NULL
        ) $charset_collate;";
        $result = $this->db->query( $sql );
        return $result;
    }

    public function delete_token_table(){
        $tableName = $this->tokenTable;
        $sql = "DROP TABLE IF EXISTS `$tableName`;";
        if ( $this->db->query($sql) ) { return true; }
        else { return false; }
    }

    public function create_address_table(){
        $charset_collate = $this->charset;
        $tableName = $this->addressTable;
        $sql = "CREATE TABLE IF NOT EXISTS $tableName (
            `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `field` ENUM('BILLING','SHIPING','OTHER') NOT NULL DEFAULT 'BILLING',
            `line1` varchar(256) DEFAULT NULL,
            `line2` varchar(128) DEFAULT NULL,
            `locality` varchar(256) DEFAULT NULL,
            `region` varchar(128) DEFAULT NULL,
            `postal_code` varchar(32) DEFAULT NULL,
            `country_code` varchar(32) DEFAULT NULL,
            `lat` varchar(64) DEFAULT NULL,
            `lng` varchar(64) DEFAULT NULL,
            `is_lat_lng` TINYINT(1) NOT NULL DEFAULT '0',
            `contact_id` int(128) NOT NULL
            ) $charset_collate;";
        $result = $this->db->query( $sql );
        return $result;
    }

    public function delete_address_table(){
        $tableName = $this->addressTable;
        $sql = "DROP TABLE IF EXISTS `$tableName`;";
        if ( $this->db->query($sql) ) { return true; }
        else { return false; }
    }

    public function create_teacher_table(){
        $charset_collate = $this->charset;
        $tableName = $this->teacherTable;
        $sql = "CREATE TABLE IF NOT EXISTS `$tableName` (
                `id` int(32) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `contact_id` int(32) DEFAULT NULL,
                `username` varchar(128) DEFAULT NULL,
                `password` varchar(128) DEFAULT NULL,
                `given_name` varchar(64) DEFAULT NULL,
                `middle_name` varchar(64) DEFAULT NULL,
                `family_name` varchar(64) DEFAULT NULL,
                `job_title` varchar(128) DEFAULT NULL,
                `phone` varchar(32) DEFAULT NULL,
                `website` varchar(128) DEFAULT NULL,
                `_YogaStudio` varchar(128) DEFAULT NULL,
                `email` text,
                `bio` text,
                `is_show_title` ENUM('YES','NO') NOT NULL DEFAULT 'NO',
                `update_values` text,
                `update_status` ENUM('PENDING','APPROVED') NOT NULL DEFAULT 'APPROVED',
                `updated_by` varchar(16) DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) $charset_collate;";
        $result = $this->db->query( $sql );
        return $result;
    }

    public function delete_teacher_table(){
        $tableName = $this->teacherTable;
        $sql = "DROP TABLE IF EXISTS `$tableName`;";
        if ( $this->db->query($sql) ) { return true; }
        else { return false; }
    }
}