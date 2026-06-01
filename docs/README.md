# Inventory Tesseract OCR System Documentation

This directory contains comprehensive documentation for the Inventory Tesseract OCR System.

## Documentation Files

- [PROJECT_OVERVIEW.md](PROJECT_OVERVIEW.md) - High-level project description, features, and architecture
- [IMPLEMENTATION_DETAILS.md](IMPLEMENTATION_DETAILS.md) - Detailed technical implementation information

## Source Documentation

The documentation in this directory was compiled from the following source files in the repository:

- `CLAUDE.md` - Project instructions and Claude Code integration guidelines
- `README.md` - Laravel framework information and project setup
- `SUMMARY.md` - Detailed implementation summary of the Inventory Tesseract OCR system
- Various files in the `.claude/` directory - Agent, skill, hook, and rule definitions

## Project Overview

This is a Laravel-based inventory management system with OCR (Optical Character Recognition) capabilities for processing receipt images. The system uses Tesseract OCR to extract text from receipt images and automatically updates inventory levels.

## Key Features

- Role-Based Access Control (users can be 'user' or 'admin')
- Inventory Management with automatic stock updates
- OCR Processing using ddeboer/tesseract package
- Data Validation workflow for corrected OCR data
- Debt Tracking for hutang (credit) purchases
- File Storage for receipt images
- Claude Code integration for AI-assisted development

## Getting Started

See [PROJECT_OVERVIEW.md](PROJECT_OVERVIEW.md) for a high-level introduction to the project.

For detailed technical implementation information, see [IMPLEMENTATION_DETAILS.md](IMPLEMENTATION_DETAILS.md).

## Claude Code Integration

This project includes Claude Code plugins that provide:
- Specialized agents for delegation (planner, code-reviewer, tdd-guide, etc.)
- Skills/workflow definitions (coding standards, patterns, testing)
- Slash commands (/tdd, /plan, /e2e, /code-review, etc.)
- Trigger-based automations (session persistence, pre/post-tool hooks)
- Always-follow guidelines (security, coding style, testing requirements)
- MCP server configurations for external integrations
- Cross-platform Node.js utilities for hooks and setup
- Test suite for scripts and utilities

## Contributing

Please refer to the contributing guidelines in the root `CONTRIBUTING.md` file (not copied to docs directory) for information on how to contribute to this project.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).