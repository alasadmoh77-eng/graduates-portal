import asyncio
import re
from playwright import async_api
from playwright.async_api import expect

async def run_test():
    pw = None
    browser = None
    context = None

    try:
        # Start a Playwright session in asynchronous mode
        pw = await async_api.async_playwright().start()

        # Launch a Chromium browser in headless mode with custom arguments
        browser = await pw.chromium.launch(
            headless=True,
            args=[
                "--window-size=1280,720",
                "--disable-dev-shm-usage",
                "--ipc=host",
                "--single-process"
            ],
        )

        # Create a new browser context (like an incognito window)
        context = await browser.new_context()
        # Wider default timeout to match the agent's DOM-stability budget;
        # auto-waiting Playwright APIs (expect, locator.wait_for) inherit this.
        context.set_default_timeout(15000)

        # Open a new page in the browser context
        page = await context.new_page()

        # Interact with the page elements to simulate user flow
        # -> navigate
        await page.goto("http://localhost:8000")
        try:
            await page.wait_for_load_state("domcontentloaded", timeout=5000)
        except Exception:
            pass
        
        # -> Click the 'دخول جهات التوظيف' (Employer login) link to open the employer sign-in page.
        # دخول جهات التوظيف link
        elem = page.locator('xpath=/html/body/nav/div/div/ul/li[5]/ul/li[2]/a')
        await elem.click(timeout=10000)
        
        # -> Fill the email field with 'employer@example.com', fill the password field with 'emp123', then click the 'تسجيل الدخول' (Login) button to sign in as an employer.
        # email email field
        elem = page.locator('xpath=/html/body/main/div/div/div/div/form/div/div/input')
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("employer@example.com")
        
        # -> Fill the email field with 'employer@example.com', fill the password field with 'emp123', then click the 'تسجيل الدخول' (Login) button to sign in as an employer.
        # password password field
        elem = page.locator('[id="password"]')
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("emp123")
        
        # -> Fill the email field with 'employer@example.com', fill the password field with 'emp123', then click the 'تسجيل الدخول' (Login) button to sign in as an employer.
        # تسجيل الدخول button
        elem = page.get_by_role('button', name='تسجيل الدخول', exact=True)
        await elem.click(timeout=10000)
        
        # -> Click the 'نشر وظيفة جديدة' (Publish New Job) button to open the job creation form.
        # نشر وظيفة جديدة link
        elem = page.get_by_role('link', name='نشر وظيفة جديدة', exact=True)
        await elem.click(timeout=10000)
        
        # -> Fill the job form by entering a job title, deadline, location, and description, then click the 'إرسال' (Submit) button to publish the job.
        # e.g. Senior PHP Developer text field
        elem = page.get_by_placeholder('e.g. Senior PHP Developer', exact=True)
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("Senior Backend Engineer")
        
        # -> Fill the job form by entering a job title, deadline, location, and description, then click the 'إرسال' (Submit) button to publish the job.
        # deadline date field
        elem = page.locator('xpath=/html/body/main/div/div/div/div/div[2]/form/div[2]/div[2]/input')
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("2026-07-31")
        
        # -> Fill the job form by entering a job title, deadline, location, and description, then click the 'إرسال' (Submit) button to publish the job.
        # e.g. Marib, Yemen text field
        elem = page.get_by_placeholder('e.g. Marib, Yemen', exact=True)
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("Marib, Yemen")
        
        # -> Fill the job form by entering a job title, deadline, location, and description, then click the 'إرسال' (Submit) button to publish the job.
        # Detailed job description... text area
        elem = page.get_by_placeholder('Detailed job description...', exact=True)
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("Automated test job: responsible for backend services and APIs.")
        
        # -> Fill the job form by entering a job title, deadline, location, and description, then click the 'إرسال' (Submit) button to publish the job.
        # إرسال button
        elem = page.get_by_role('button', name='إرسال', exact=True)
        await elem.click(timeout=10000)
        
        # --> Assertions to verify final state
        
        # --> Verify the new job listing appears in the employer jobs area
        await page.locator("xpath=/html/body/main/div/div[3]/div/div/table/tbody/tr[1]").nth(0).scroll_into_view_if_needed()
        # Assert: The newly created job row is visible in the employer jobs table.
        await expect(page.locator("xpath=/html/body/main/div/div[3]/div/div/table/tbody/tr[1]").nth(0)).to_be_visible(timeout=15000), "The newly created job row is visible in the employer jobs table."
        # Assert: The job title 'Senior Backend Engineer' appears in the employer jobs list.
        await expect(page.locator("xpath=/html/body/main/div/div[3]/div/div/table/tbody/tr[1]/td[1]").nth(0)).to_contain_text("Senior Backend Engineer", timeout=15000), "The job title 'Senior Backend Engineer' appears in the employer jobs list."
        # Assert: The job location 'Marib, Yemen' appears alongside the job listing.
        await expect(page.locator("xpath=/html/body/main/div/div[3]/div/div/table/tbody/tr[1]/td[1]").nth(0)).to_contain_text("Marib, Yemen", timeout=15000), "The job location 'Marib, Yemen' appears alongside the job listing."
        # Assert: The job status is 'Pending' in the employer jobs list.
        await expect(page.locator("xpath=/html/body/main/div/div[3]/div/div/table/tbody/tr[1]/td[3]").nth(0)).to_have_text("Pending", timeout=15000), "The job status is 'Pending' in the employer jobs list."
        current_url = await page.evaluate("() => window.location.href")
        # Assert: page loaded with a URL (final outcome verified by the AI judge during the run)
        assert current_url, 'Page should have loaded with a URL'
        await asyncio.sleep(5)

    finally:
        if context:
            await context.close()
        if browser:
            await browser.close()
        if pw:
            await pw.stop()

asyncio.run(run_test())
    