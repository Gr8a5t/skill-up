# SkillUp Frontend Developer Guide (React + TypeScript + Inertia.js)

Welcome! This project has been upgraded from Blade template views to a single-page application (SPA) shell using **React**, **TypeScript**, and **Inertia.js**. 

This guide will help you understand the directory structure and how to add pages easily.

---

## 🛠 Tech Stack

1. **Backend**: Laravel 11
2. **Frontend SPA Link**: Inertia.js (translates backend controller actions directly to frontend React views without needing a REST/GraphQL API).
3. **Frontend**: React 19 + TypeScript (strict typing for safety).
4. **Styling**: Global Vanilla CSS (`public/fitlife-assets/css/style.css`).

---

## 📁 Directory Structure

All frontend code lives under `resources/js/`:

```
resources/js/
├── Components/         # Reusable frontend UI components (Header, Footer, etc.)
│   ├── Header.tsx
│   └── Footer.tsx
├── Layouts/            # Shared page layouts
│   └── FitLifeLayout.tsx
├── Pages/              # Page components loaded by Laravel/Inertia
│   ├── Home.tsx
│   ├── About.tsx
│   └── Welcome.tsx
├── app.tsx             # Application entry point & Inertia bootstrapper
├── bootstrap.ts        # Axios configuration
└── tsconfig.json       # TypeScript compiler settings (root folder)
```

---

## 🚀 How to Add a New Page

To add a new page (e.g. "Courses"), follow these three simple steps:

### Step 1: Create the React Component
Create a new file in `resources/js/Pages/Courses.tsx`. 

```tsx
import React from 'react';
import { Head } from '@inertiajs/react';
import FitLifeLayout from '../Layouts/FitLifeLayout';

interface Course {
    id: number;
    title: string;
}

interface CoursesProps {
    courses: Course[];
}

export default function Courses({ courses }: CoursesProps) {
    return (
        <FitLifeLayout>
            <Head title="Browse Courses" />
            <main style={{ padding: '120px 20px 60px' }} className="container">
                <h1>Courses</h1>
                <ul>
                    {courses.map(course => (
                        <li key={course.id}>{course.title}</li>
                    ))}
                </ul>
            </main>
        </FitLifeLayout>
    );
}
```

### Step 2: Define the Route in Laravel Controller
In your Laravel controller (e.g. `FitlifeController.php`), import the `Inertia` class and return the component. Inertia will look directly in `resources/js/Pages/` for a file named `Courses.tsx`.

```php
use Inertia\Inertia;

public function courses()
{
    $courses = [
        ['id' => 1, 'title' => 'HTML Basics'],
        ['id' => 2, 'title' => 'CSS Styling'],
    ];

    // Renders resources/js/Pages/Courses.tsx
    return Inertia::render('Courses', [
        'courses' => $courses
    ]);
}
```

### Step 3: Run the Compiler
During local development, make sure you have the hot-reload compiler running in a terminal:
```bash
npm run dev
```

To build production assets for deployment, run:
```bash
npm run build
```

---

## 🔗 Key Inertia Utilities

* **Page Transitions**: Instead of standard HTML `<a href="...">`, always import and use `<Link href="...">` from `@inertiajs/react`. This enables instant transitions without full browser reloads.
* **Metadata**: Use `<Head title="Your Page Title" />` to dynamically manage document head meta and title tags.
* **Shared Authentication State**: Current user session details are passed automatically to every page. You can access them in any component like this:
  ```typescript
  import { usePage } from '@inertiajs/react';
  const { props } = usePage();
  const user = props.auth.user; // Contains the logged-in user object or null
  ```
