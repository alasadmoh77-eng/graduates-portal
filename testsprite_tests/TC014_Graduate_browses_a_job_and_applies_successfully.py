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
        
        # -> Click the 'تسجيل الدخول' (Login) link in the header to open the login page.
        # تسجيل الدخول link
        elem = page.locator('xpath=/html/body/nav/div/div/ul/li[6]/a')
        await elem.click(timeout=10000)
        
        # -> Fill the Email field with graduate@example.com, fill the Password field with grad123, then click the 'تسجيل الدخول' (Login) button to submit the graduate login.
        # email email field
        elem = page.locator('xpath=/html/body/main/div/div/div/div/form/div/div/input')
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("graduate@example.com")
        
        # -> Fill the Email field with graduate@example.com, fill the Password field with grad123, then click the 'تسجيل الدخول' (Login) button to submit the graduate login.
        # password password field
        elem = page.locator('[id="password"]')
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("grad123")
        
        # -> Fill the Email field with graduate@example.com, fill the Password field with grad123, then click the 'تسجيل الدخول' (Login) button to submit the graduate login.
        # تسجيل الدخول button
        elem = page.get_by_role('button', name='تسجيل الدخول', exact=True)
        await elem.click(timeout=10000)
        
        # -> Click the 'فرص العمل' (Jobs) link in the site header to open the graduate job listings page.
        # فرص العمل link
        elem = page.get_by_text('لوحة التحكم', exact=True).locator("xpath=ancestor-or-self::*[.//a][1]").get_by_role('link', name='فرص العمل', exact=True)
        await elem.click(timeout=10000)
        
        # -> Scroll the job listings page slightly to ensure the first job card's 'التفاصيل والتقديم' (Details & Apply) and 'تقديم الطلب' (Submit application) buttons are fully visible, then click the 'التفاصيل والتقديم' button for the first job.
        await page.mouse.wheel(0, 300)
        
        # -> Click the 'التفاصيل والتقديم' (Details & Apply) link on the Software Developer job card to open its details page or modal.
        # التفاصيل والتقديم link
        elem = page.get_by_text('Full-time Deadline: 2026-07-21', exact=True).locator("xpath=ancestor-or-self::*[.//a][1]").get_by_role('link', name='التفاصيل والتقديم', exact=True)
        await elem.click(timeout=10000)
        
        # -> Fill the cover letter field with a short message and click the 'إرسال الطلب' (Submit application) button to submit the application, then check for a success confirmation message.
        # اشرح باختصار لماذا أنت مهتم بهذه الوظيفة... text area
        elem = page.get_by_placeholder('اشرح باختصار لماذا أنت مهتم بهذه الوظيفة...', exact=True)
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("\u0623\u0646\u0627 \u0645\u0647\u062a\u0645 \u0628\u0647\u0630\u0647 \u0627\u0644\u0648\u0638\u064a\u0641\u0629 \u0644\u0623\u0646 \u0644\u062f\u064a \u062e\u0628\u0631\u0629 \u0641\u064a \u062a\u0637\u0648\u064a\u0631 \u0627\u0644\u0648\u0627\u062c\u0647\u0627\u062a \u0648\u0627\u0644\u062e\u0648\u0627\u062f\u0645 \u0628\u0627\u0633\u062a\u062e\u062f\u0627\u0645 Laravel \u0648Vue.js\u060c \u0648\u0644\u062f\u064a \u0633\u062c\u0644 \u0639\u0645\u0644 \u0639\u0645\u0644\u064a \u0639\u0644\u0649 \u0645\u0634\u0627\u0631\u064a\u0639 \u0645\u0634\u0627\u0628\u0647\u0629. \u0623\u062a\u0637\u0644\u0639 \u0644\u0644\u0645\u0633\u0627\u0647\u0645\u0629 \u0641\u064a \u0641\u0631\u064a\u0642 Tech Solutions Ltd.")
        
        # -> Fill the cover letter field with a short message and click the 'إرسال الطلب' (Submit application) button to submit the application, then check for a success confirmation message.
        # إرسال الطلب button
        elem = page.get_by_role('button', name='إرسال الطلب', exact=True)
        await elem.click(timeout=10000)
        
        # --> Assertions to verify final state
        
        # --> Verify the application action is completed
        # Assert: Expected URL to contain "/graduate/applications" indicating the application was completed.
        await expect(page).to_have_url(re.compile("/graduate/applications"), timeout=15000), "Expected URL to contain \"/graduate/applications\" indicating the application was completed."
        
        # --> Verify a success confirmation is visible
        # Assert: Expected a visible success confirmation saying 'تم إرسال الطلب'.
        await expect(page.locator("xpath=/html/body/div[2]").nth(0)).to_contain_text("\u062a\u0645 \u0625\u0631\u0633\u0627\u0644 \u0627\u0644\u0637\u0644\u0628", timeout=15000), "Expected a visible success confirmation saying '\u062a\u0645 \u0625\u0631\u0633\u0627\u0644 \u0627\u0644\u0637\u0644\u0628'."
        
        # --> Test blocked by environment/access constraints during agent run
        # Reason: TEST BLOCKED The application could not be submitted because a required CV file is missing from the account and no local file is available to upload. Observations: - After clicking 'إرسال الطلب' a top warning says 'لا توجد سيرة ذاتية في ملفك' (No CV in your profile). - The application form includes a CV file input (accepts .pdf, .doc, .docx) but no file was attached. To complete the test, provid...
        raise AssertionError("Test blocked during agent run: " + "TEST BLOCKED The application could not be submitted because a required CV file is missing from the account and no local file is available to upload. Observations: - After clicking '\u0625\u0631\u0633\u0627\u0644 \u0627\u0644\u0637\u0644\u0628' a top warning says '\u0644\u0627 \u062a\u0648\u062c\u062f \u0633\u064a\u0631\u0629 \u0630\u0627\u062a\u064a\u0629 \u0641\u064a \u0645\u0644\u0641\u0643' (No CV in your profile). - The application form includes a CV file input (accepts .pdf, .doc, .docx) but no file was attached. To complete the test, provid..." + " — the exported script cannot reproduce a PASS in this environment.")
        await asyncio.sleep(5)

    finally:
        if context:
            await context.close()
        if browser:
            await browser.close()
        if pw:
            await pw.stop()

asyncio.run(run_test())
    