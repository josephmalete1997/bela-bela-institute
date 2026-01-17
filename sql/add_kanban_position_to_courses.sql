USE belabela_iHL;

ALTER TABLE courses
  ADD COLUMN IF NOT EXISTS kanban_position INT NOT NULL DEFAULT 0;
