USE belabela_iHL;

ALTER TABLE courses
  ADD COLUMN IF NOT EXISTS kanban_status VARCHAR(32) NOT NULL DEFAULT 'backlog';
