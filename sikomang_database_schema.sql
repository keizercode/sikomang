-- =====================================================
-- SIKOMANG DATABASE SCHEMA - PostgreSQL
-- Sistem Informasi dan Komunikasi Mangrove DKI Jakarta
-- =====================================================

-- Enable UUID extension
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE EXTENSION IF NOT EXISTS "postgis"; -- For geospatial data

-- =====================================================
-- 1. USERS & AUTHENTICATION
-- =====================================================

CREATE TABLE users (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    avatar_url TEXT,
    role VARCHAR(50) DEFAULT 'user' CHECK (role IN ('admin', 'officer', 'community', 'user')),
    is_active BOOLEAN DEFAULT true,
    remember_token VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);

-- =====================================================
-- 2. GEOGRAPHIC & ADMINISTRATIVE DATA
-- =====================================================

CREATE TABLE regions (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL CHECK (type IN ('kecamatan', 'kelurahan', 'kepulauan')),
    code VARCHAR(20) UNIQUE,
    parent_id UUID REFERENCES regions(id) ON DELETE SET NULL,
    geom GEOMETRY(MULTIPOLYGON, 4326), -- Polygon boundary
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_regions_type ON regions(type);
CREATE INDEX idx_regions_parent ON regions(parent_id);
CREATE INDEX idx_regions_geom ON regions USING GIST(geom);

-- =====================================================
-- 3. MANGROVE LOCATIONS & SITES
-- =====================================================

CREATE TABLE mangrove_sites (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    slug VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    region_id UUID REFERENCES regions(id) ON DELETE SET NULL,
    
    -- Geographic data
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    geom POINT, -- PostGIS point
    area_hectares DECIMAL(10, 2),
    
    -- Classification
    density_category VARCHAR(20) CHECK (density_category IN ('jarang', 'sedang', 'lebat')),
    site_type VARCHAR(50) CHECK (site_type IN ('pengkayaan', 'rehabilitasi', 'dilindungi', 'restorasi')),
    forest_function VARCHAR(10) CHECK (forest_function IN ('APL', 'HL', 'HP', 'TN', 'SM', 'TWA')),
    conservation_status VARCHAR(50),
    
    -- Management
    manager VARCHAR(255),
    year_established INTEGER,
    
    -- Health metrics
    health_percentage DECIMAL(5, 2),
    nak_score DECIMAL(4, 2), -- Nilai Akhir Kesehatan
    
    -- Carbon data
    carbon_storage_tons DECIMAL(10, 2),
    carbon_absorption_rate DECIMAL(10, 2),
    
    -- Description
    description TEXT,
    location_address TEXT,
    
    -- Metadata
    is_active BOOLEAN DEFAULT true,
    created_by UUID REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_mangrove_sites_slug ON mangrove_sites(slug);
CREATE INDEX idx_mangrove_sites_region ON mangrove_sites(region_id);
CREATE INDEX idx_mangrove_sites_density ON mangrove_sites(density_category);
CREATE INDEX idx_mangrove_sites_type ON mangrove_sites(site_type);
CREATE INDEX idx_mangrove_sites_geom ON mangrove_sites USING GIST(geom);

-- =====================================================
-- 4. SPECIES DATA (Flora & Fauna)
-- =====================================================

CREATE TABLE species (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    scientific_name VARCHAR(255) NOT NULL,
    common_name VARCHAR(255),
    type VARCHAR(20) CHECK (type IN ('vegetasi', 'fauna')),
    category VARCHAR(50), -- e.g., 'pohon', 'burung', 'ikan', etc.
    conservation_status VARCHAR(50),
    description TEXT,
    image_url TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_species_type ON species(type);
CREATE INDEX idx_species_scientific ON species(scientific_name);

-- =====================================================
-- 5. SITE-SPECIES RELATIONSHIP
-- =====================================================

CREATE TABLE site_species (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    site_id UUID REFERENCES mangrove_sites(id) ON DELETE CASCADE,
    species_id UUID REFERENCES species(id) ON DELETE CASCADE,
    abundance VARCHAR(20) CHECK (abundance IN ('langka', 'sedang', 'banyak', 'dominan')),
    notes TEXT,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    recorded_by UUID REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(site_id, species_id)
);

CREATE INDEX idx_site_species_site ON site_species(site_id);
CREATE INDEX idx_site_species_species ON site_species(species_id);

-- =====================================================
-- 6. MONITORING & OBSERVATIONS
-- =====================================================

CREATE TABLE monitoring_sessions (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    site_id UUID REFERENCES mangrove_sites(id) ON DELETE CASCADE,
    session_date DATE NOT NULL,
    observer_id UUID REFERENCES users(id) ON DELETE SET NULL,
    
    -- Weather conditions
    weather VARCHAR(50),
    temperature_celsius DECIMAL(4, 2),
    tide_condition VARCHAR(50),
    
    -- Observations
    notes TEXT,
    recommendations TEXT,
    
    -- Status
    status VARCHAR(20) DEFAULT 'draft' CHECK (status IN ('draft', 'submitted', 'verified', 'published')),
    verified_by UUID REFERENCES users(id) ON DELETE SET NULL,
    verified_at TIMESTAMP,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_monitoring_site ON monitoring_sessions(site_id);
CREATE INDEX idx_monitoring_date ON monitoring_sessions(session_date);
CREATE INDEX idx_monitoring_status ON monitoring_sessions(status);

-- =====================================================
-- 7. DAMAGE REPORTS
-- =====================================================

CREATE TABLE damage_reports (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    site_id UUID REFERENCES mangrove_sites(id) ON DELETE CASCADE,
    monitoring_session_id UUID REFERENCES monitoring_sessions(id) ON DELETE SET NULL,
    
    -- Damage details
    damage_type VARCHAR(100) NOT NULL,
    severity VARCHAR(20) CHECK (severity IN ('ringan', 'sedang', 'berat', 'kritis')),
    affected_area_hectares DECIMAL(10, 2),
    description TEXT NOT NULL,
    
    -- Location
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    
    -- Cause
    cause VARCHAR(255),
    suspected_cause TEXT,
    
    -- Response
    immediate_action TEXT,
    required_action TEXT,
    estimated_cost DECIMAL(12, 2),
    
    -- Status tracking
    status VARCHAR(20) DEFAULT 'reported' CHECK (status IN ('reported', 'investigated', 'in_progress', 'resolved', 'closed')),
    priority VARCHAR(20) DEFAULT 'medium' CHECK (priority IN ('low', 'medium', 'high', 'urgent')),
    
    -- Assignment
    reported_by UUID REFERENCES users(id) ON DELETE SET NULL,
    assigned_to UUID REFERENCES users(id) ON DELETE SET NULL,
    resolved_at TIMESTAMP,
    resolution_notes TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_damage_site ON damage_reports(site_id);
CREATE INDEX idx_damage_status ON damage_reports(status);
CREATE INDEX idx_damage_priority ON damage_reports(priority);
CREATE INDEX idx_damage_severity ON damage_reports(severity);

-- =====================================================
-- 8. CONSERVATION ACTIVITIES
-- =====================================================

CREATE TABLE activities (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    site_id UUID REFERENCES mangrove_sites(id) ON DELETE CASCADE,
    
    -- Activity details
    title VARCHAR(255) NOT NULL,
    activity_type VARCHAR(50) CHECK (activity_type IN ('penanaman', 'pembersihan', 'pemantauan', 'edukasi', 'penelitian', 'rehabilitasi')),
    description TEXT,
    
    -- Planning
    planned_date DATE,
    planned_participants INTEGER,
    planned_budget DECIMAL(12, 2),
    
    -- Execution
    actual_date DATE,
    actual_participants INTEGER,
    actual_budget DECIMAL(12, 2),
    
    -- Results
    seedlings_planted INTEGER,
    area_covered_hectares DECIMAL(10, 2),
    waste_collected_kg DECIMAL(10, 2),
    success_notes TEXT,
    
    -- Coordination
    organizer_id UUID REFERENCES users(id) ON DELETE SET NULL,
    status VARCHAR(20) DEFAULT 'planned' CHECK (status IN ('planned', 'ongoing', 'completed', 'cancelled')),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_activities_site ON activities(site_id);
CREATE INDEX idx_activities_type ON activities(activity_type);
CREATE INDEX idx_activities_status ON activities(status);
CREATE INDEX idx_activities_date ON activities(planned_date);

-- =====================================================
-- 9. ACTIVITY PARTICIPANTS
-- =====================================================

CREATE TABLE activity_participants (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    activity_id UUID REFERENCES activities(id) ON DELETE CASCADE,
    user_id UUID REFERENCES users(id) ON DELETE CASCADE,
    role VARCHAR(50) DEFAULT 'participant' CHECK (role IN ('organizer', 'coordinator', 'volunteer', 'participant')),
    attendance_status VARCHAR(20) DEFAULT 'registered' CHECK (attendance_status IN ('registered', 'attended', 'absent', 'cancelled')),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(activity_id, user_id)
);

CREATE INDEX idx_activity_participants_activity ON activity_participants(activity_id);
CREATE INDEX idx_activity_participants_user ON activity_participants(user_id);

-- =====================================================
-- 10. STAKEHOLDERS & PARTNERSHIPS
-- =====================================================

CREATE TABLE stakeholders (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(255) NOT NULL,
    type VARCHAR(50) CHECK (type IN ('pemerintah', 'swasta', 'komunitas', 'akademisi', 'ngo', 'internasional')),
    category VARCHAR(100),
    
    -- Contact
    contact_person VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(20),
    address TEXT,
    website VARCHAR(255),
    
    -- Details
    description TEXT,
    logo_url TEXT,
    
    -- Status
    is_active BOOLEAN DEFAULT true,
    partnership_start_date DATE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_stakeholders_type ON stakeholders(type);
CREATE INDEX idx_stakeholders_active ON stakeholders(is_active);

-- =====================================================
-- 11. SITE-STAKEHOLDER RELATIONSHIP
-- =====================================================

CREATE TABLE site_stakeholders (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    site_id UUID REFERENCES mangrove_sites(id) ON DELETE CASCADE,
    stakeholder_id UUID REFERENCES stakeholders(id) ON DELETE CASCADE,
    role VARCHAR(100), -- e.g., 'sponsor', 'implementer', 'researcher'
    contribution TEXT,
    start_date DATE,
    end_date DATE,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(site_id, stakeholder_id)
);

CREATE INDEX idx_site_stakeholders_site ON site_stakeholders(site_id);
CREATE INDEX idx_site_stakeholders_stakeholder ON site_stakeholders(stakeholder_id);

-- =====================================================
-- 12. PROGRAMS & INITIATIVES
-- =====================================================

CREATE TABLE programs (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(255) NOT NULL,
    program_type VARCHAR(100),
    description TEXT,
    objectives TEXT,
    
    -- Timeline
    start_date DATE,
    end_date DATE,
    
    -- Budget
    total_budget DECIMAL(14, 2),
    spent_budget DECIMAL(14, 2),
    
    -- Management
    coordinator_id UUID REFERENCES users(id) ON DELETE SET NULL,
    status VARCHAR(20) DEFAULT 'active' CHECK (status IN ('planned', 'active', 'completed', 'suspended', 'cancelled')),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_programs_status ON programs(status);
CREATE INDEX idx_programs_dates ON programs(start_date, end_date);

-- =====================================================
-- 13. SITE-PROGRAM RELATIONSHIP
-- =====================================================

CREATE TABLE site_programs (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    site_id UUID REFERENCES mangrove_sites(id) ON DELETE CASCADE,
    program_id UUID REFERENCES programs(id) ON DELETE CASCADE,
    implementation_notes TEXT,
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(site_id, program_id)
);

CREATE INDEX idx_site_programs_site ON site_programs(site_id);
CREATE INDEX idx_site_programs_program ON site_programs(program_id);

-- =====================================================
-- 14. MEDIA & DOCUMENTATION
-- =====================================================

CREATE TABLE media (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    
    -- References (polymorphic)
    mediable_type VARCHAR(50) NOT NULL, -- 'site', 'activity', 'damage_report', 'monitoring', 'article'
    mediable_id UUID NOT NULL,
    
    -- File details
    file_name VARCHAR(255) NOT NULL,
    file_path TEXT NOT NULL,
    file_type VARCHAR(50), -- 'image', 'video', 'document', 'audio'
    mime_type VARCHAR(100),
    file_size_bytes BIGINT,
    
    -- Media info
    title VARCHAR(255),
    description TEXT,
    alt_text VARCHAR(255),
    
    -- Image specific
    width INTEGER,
    height INTEGER,
    
    -- Ordering
    display_order INTEGER DEFAULT 0,
    is_featured BOOLEAN DEFAULT false,
    
    -- Metadata
    uploaded_by UUID REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_media_mediable ON media(mediable_type, mediable_id);
CREATE INDEX idx_media_type ON media(file_type);
CREATE INDEX idx_media_featured ON media(is_featured);

-- =====================================================
-- 15. ARTICLES & CONTENT
-- =====================================================

CREATE TABLE articles (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    slug VARCHAR(255) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    excerpt TEXT,
    content TEXT NOT NULL,
    
    -- Classification
    category VARCHAR(50) CHECK (category IN ('konservasi', 'edukasi', 'kegiatan', 'berita', 'penelitian')),
    tags TEXT[], -- Array of tags
    
    -- SEO
    meta_description TEXT,
    meta_keywords TEXT[],
    
    -- Featured image
    featured_image_url TEXT,
    
    -- Publishing
    author_id UUID REFERENCES users(id) ON DELETE SET NULL,
    status VARCHAR(20) DEFAULT 'draft' CHECK (status IN ('draft', 'published', 'archived')),
    published_at TIMESTAMP,
    
    -- Engagement
    view_count INTEGER DEFAULT 0,
    like_count INTEGER DEFAULT 0,
    share_count INTEGER DEFAULT 0,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_articles_slug ON articles(slug);
CREATE INDEX idx_articles_category ON articles(category);
CREATE INDEX idx_articles_status ON articles(status);
CREATE INDEX idx_articles_published ON articles(published_at);
CREATE INDEX idx_articles_tags ON articles USING GIN(tags);

-- =====================================================
-- 16. COMMENTS & ENGAGEMENT
-- =====================================================

CREATE TABLE comments (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    
    -- Polymorphic relationship
    commentable_type VARCHAR(50) NOT NULL, -- 'article', 'activity', 'site'
    commentable_id UUID NOT NULL,
    
    -- Comment content
    content TEXT NOT NULL,
    
    -- Threading
    parent_id UUID REFERENCES comments(id) ON DELETE CASCADE,
    
    -- User
    user_id UUID REFERENCES users(id) ON DELETE CASCADE,
    
    -- Moderation
    status VARCHAR(20) DEFAULT 'pending' CHECK (status IN ('pending', 'approved', 'rejected', 'spam')),
    moderated_by UUID REFERENCES users(id) ON DELETE SET NULL,
    moderated_at TIMESTAMP,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_comments_commentable ON comments(commentable_type, commentable_id);
CREATE INDEX idx_comments_user ON comments(user_id);
CREATE INDEX idx_comments_status ON comments(status);
CREATE INDEX idx_comments_parent ON comments(parent_id);

-- =====================================================
-- 17. NOTIFICATIONS
-- =====================================================

CREATE TABLE notifications (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID REFERENCES users(id) ON DELETE CASCADE,
    
    -- Notification content
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    
    -- Action link
    action_url TEXT,
    
    -- Related entity (optional)
    related_type VARCHAR(50),
    related_id UUID,
    
    -- Status
    is_read BOOLEAN DEFAULT false,
    read_at TIMESTAMP,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_notifications_user ON notifications(user_id);
CREATE INDEX idx_notifications_read ON notifications(is_read);
CREATE INDEX idx_notifications_created ON notifications(created_at);

-- =====================================================
-- 18. AUDIT LOG
-- =====================================================

CREATE TABLE audit_logs (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID REFERENCES users(id) ON DELETE SET NULL,
    
    -- Action details
    action VARCHAR(50) NOT NULL, -- 'create', 'update', 'delete', 'login', etc.
    model_type VARCHAR(50) NOT NULL,
    model_id UUID,
    
    -- Changes
    old_values JSONB,
    new_values JSONB,
    
    -- Context
    ip_address INET,
    user_agent TEXT,
    url TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_audit_user ON audit_logs(user_id);
CREATE INDEX idx_audit_action ON audit_logs(action);
CREATE INDEX idx_audit_model ON audit_logs(model_type, model_id);
CREATE INDEX idx_audit_created ON audit_logs(created_at);

-- =====================================================
-- 19. SETTINGS & CONFIGURATIONS
-- =====================================================

CREATE TABLE settings (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    key VARCHAR(100) UNIQUE NOT NULL,
    value JSONB,
    description TEXT,
    is_public BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_settings_key ON settings(key);

-- =====================================================
-- 20. SESSIONS (Laravel)
-- =====================================================

CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id UUID REFERENCES users(id) ON DELETE CASCADE,
    ip_address VARCHAR(45),
    user_agent TEXT,
    payload TEXT NOT NULL,
    last_activity INTEGER NOT NULL
);

CREATE INDEX idx_sessions_user ON sessions(user_id);
CREATE INDEX idx_sessions_last_activity ON sessions(last_activity);

-- =====================================================
-- 21. PASSWORD RESET TOKENS
-- =====================================================

CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- 22. CACHE (Laravel)
-- =====================================================

CREATE TABLE cache (
    key VARCHAR(255) PRIMARY KEY,
    value TEXT NOT NULL,
    expiration INTEGER NOT NULL
);

CREATE TABLE cache_locks (
    key VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INTEGER NOT NULL
);

-- =====================================================
-- 23. JOBS & QUEUES (Laravel)
-- =====================================================

CREATE TABLE jobs (
    id BIGSERIAL PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload TEXT NOT NULL,
    attempts SMALLINT NOT NULL DEFAULT 0,
    reserved_at INTEGER,
    available_at INTEGER NOT NULL,
    created_at INTEGER NOT NULL
);

CREATE INDEX idx_jobs_queue ON jobs(queue);

CREATE TABLE job_batches (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    total_jobs INTEGER NOT NULL,
    pending_jobs INTEGER NOT NULL,
    failed_jobs INTEGER NOT NULL,
    failed_job_ids TEXT NOT NULL,
    options TEXT,
    cancelled_at INTEGER,
    created_at INTEGER NOT NULL,
    finished_at INTEGER
);

CREATE TABLE failed_jobs (
    id BIGSERIAL PRIMARY KEY,
    uuid VARCHAR(255) UNIQUE NOT NULL,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload TEXT NOT NULL,
    exception TEXT NOT NULL,
    failed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_failed_jobs_uuid ON failed_jobs(uuid);

-- =====================================================
-- TRIGGERS FOR UPDATED_AT
-- =====================================================

CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Apply trigger to all tables with updated_at
DO $$
DECLARE
    t TEXT;
BEGIN
    FOR t IN 
        SELECT table_name 
        FROM information_schema.columns 
        WHERE column_name = 'updated_at' 
        AND table_schema = 'public'
    LOOP
        EXECUTE format('
            CREATE TRIGGER update_%I_updated_at 
            BEFORE UPDATE ON %I 
            FOR EACH ROW 
            EXECUTE FUNCTION update_updated_at_column()',
            t, t
        );
    END LOOP;
END;
$$;

-- =====================================================
-- VIEWS FOR COMMON QUERIES
-- =====================================================

-- View: Site Overview with Statistics
CREATE OR REPLACE VIEW vw_site_overview AS
SELECT 
    ms.id,
    ms.slug,
    ms.name,
    ms.latitude,
    ms.longitude,
    ms.area_hectares,
    ms.density_category,
    ms.site_type,
    ms.health_percentage,
    ms.nak_score,
    r.name as region_name,
    r.type as region_type,
    COUNT(DISTINCT dr.id) as damage_count,
    COUNT(DISTINCT a.id) as activity_count,
    COUNT(DISTINCT ss.id) as species_count
FROM mangrove_sites ms
LEFT JOIN regions r ON ms.region_id = r.id
LEFT JOIN damage_reports dr ON ms.id = dr.site_id AND dr.status != 'closed'
LEFT JOIN activities a ON ms.id = a.site_id
LEFT JOIN site_species ss ON ms.id = ss.site_id
WHERE ms.is_active = true
GROUP BY ms.id, r.id;

-- View: Monthly Activity Summary
CREATE OR REPLACE VIEW vw_monthly_activity_summary AS
SELECT 
    DATE_TRUNC('month', planned_date) as month,
    activity_type,
    COUNT(*) as activity_count,
    SUM(actual_participants) as total_participants,
    SUM(seedlings_planted) as total_seedlings,
    SUM(area_covered_hectares) as total_area_covered
FROM activities
WHERE status = 'completed'
GROUP BY DATE_TRUNC('month', planned_date), activity_type;

-- =====================================================
-- INITIAL DATA SEEDING
-- =====================================================

-- Insert default admin user (password should be hashed in application)
INSERT INTO users (name, email, role, email_verified_at) 
VALUES ('Admin SIKOMANG', 'admin@sikomang.id', 'admin', CURRENT_TIMESTAMP);

-- Insert default settings
INSERT INTO settings (key, value, description, is_public) VALUES
('site_name', '"SIKOMANG"', 'Site name', true),
('site_description', '"Sistem Informasi dan Komunikasi Mangrove DKI Jakarta"', 'Site description', true),
('items_per_page', '10', 'Default pagination', true),
('maintenance_mode', 'false', 'Maintenance mode status', false);

-- =====================================================
-- COMMENTS
-- =====================================================

COMMENT ON TABLE mangrove_sites IS 'Main table storing mangrove site locations and details';
COMMENT ON TABLE damage_reports IS 'Records of damage identified at mangrove sites';
COMMENT ON TABLE activities IS 'Conservation and maintenance activities';
COMMENT ON TABLE monitoring_sessions IS 'Regular monitoring observation sessions';
COMMENT ON TABLE species IS 'Master data of flora and fauna species';
COMMENT ON TABLE stakeholders IS 'Organizations and individuals involved in conservation';
COMMENT ON TABLE programs IS 'Long-term conservation programs and initiatives';

COMMENT ON COLUMN mangrove_sites.nak_score IS 'Nilai Akhir Kesehatan - Final health score (0-10)';
COMMENT ON COLUMN mangrove_sites.geom IS 'PostGIS geometry point for spatial queries';
COMMENT ON COLUMN damage_reports.severity IS 'Severity level: ringan, sedang, berat, kritis';
