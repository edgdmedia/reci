# Development Branch Policy

## Branch Roles
- `development`: primary shared integration branch for ongoing work
- `main`: stable release branch only
- feature branches: short-lived branches merged into `development`

## Promotion Flow
1. Work lands on a feature branch
2. Feature branch merges into `development`
3. `development` is verified
4. `development` merges into `main`
5. Theme release/package is created from `main`

## Rules
- Do not package production releases from feature branches
- Do not push unreviewed recovery or migration history to `main`
- Use `development` for day-to-day pushes once remote tracking is established
