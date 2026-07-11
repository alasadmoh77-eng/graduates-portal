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
        
        # -> Click the 'تسجيل جهة توظيف' (Register employer) link in the 'الخدمات الوظيفية' menu to open the employer registration page.
        # تسجيل جهة توظيف link
        elem = page.locator('xpath=/html/body/nav/div/div/ul/li[5]/ul/li/a')
        await elem.click(timeout=10000)
        
        # -> Fill the 'اسم ممثل الجهة' (Representative name) field with the employer representative name 'Test Employer Rep'.
        # name text field
        elem = page.locator('xpath=/html/body/main/div/div/div/div/form/div/div/input')
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("Test Employer Rep")
        
        # -> Fill the registration email field with a new unique email (employer+20260626@example.com) and fill the visible company fields (Company name, Industry, Phone, Address).
        # email email field
        elem = page.locator('xpath=/html/body/main/div/div/div/div/form/div/div[2]/input')
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("employer+20260626@example.com")
        
        # -> Fill the registration email field with a new unique email (employer+20260626@example.com) and fill the visible company fields (Company name, Industry, Phone, Address).
        # company_name text field
        elem = page.locator('xpath=/html/body/main/div/div/div/div/form/div[2]/div/input')
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("Test Employer Co")
        
        # -> Fill the registration email field with a new unique email (employer+20260626@example.com) and fill the visible company fields (Company name, Industry, Phone, Address).
        # مثال: تقنية معلومات، تعليم، صحة text field
        elem = page.get_by_placeholder('مثال: تقنية معلومات، تعليم، صحة', exact=True)
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("\u062a\u0642\u0646\u064a\u0629 \u0645\u0639\u0644\u0648\u0645\u0627\u062a")
        
        # -> Fill the registration email field with a new unique email (employer+20260626@example.com) and fill the visible company fields (Company name, Industry, Phone, Address).
        # phone text field
        elem = page.locator('xpath=/html/body/main/div/div/div/div/form/div[3]/div/input')
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("+9676302008")
        
        # -> Fill the registration email field with a new unique email (employer+20260626@example.com) and fill the visible company fields (Company name, Industry, Phone, Address).
        # address text field
        elem = page.locator('xpath=/html/body/main/div/div/div/div/form/div[4]/div/input')
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("\u0645\u0623\u0631\u0628\u060c \u0627\u0644\u064a\u0645\u0646")
        
        # -> input
        # description text area
        elem = page.locator('xpath=/html/body/main/div/div/div/div/form/div[5]/div/textarea')
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("\u0634\u0631\u0643\u0629 \u062a\u062c\u0631\u064a\u0628\u064a\u0629 \u0644\u062a\u0633\u062c\u064a\u0644 \u062c\u0647\u0629 \u062a\u0648\u0638\u064a\u0641 \u0648\u062a\u0648\u0641\u064a\u0631 \u0641\u0631\u0635 \u0639\u0645\u0644 \u0644\u0644\u062e\u0631\u064a\u062c\u064a\u0646.")
        
        # -> input
        # password password field
        elem = page.locator('xpath=/html/body/main/div/div/div/div/form/div[7]/div/input')
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("emp12345")
        
        # -> input
        # password_confirmation password field
        elem = page.locator('xpath=/html/body/main/div/div/div/div/form/div[7]/div[2]/input')
        await elem.wait_for(state="visible", timeout=10000)
        await elem.fill("emp12345")
        
        # -> click
        # تسجيل خريج جديد button
        elem = page.get_by_role('button', name='تسجيل خريج جديد', exact=True)
        await elem.click(timeout=10000)
        
        # --> Assertions to verify final state
        
        # --> Verify the employer is taken to the authenticated employer area or sees a registration confirmation state
        # Assert: Expected the browser to navigate to the employer dashboard after registration.
        await expect(page).to_have_url(re.compile("/employers/dashboard"), timeout=15000), "Expected the browser to navigate to the employer dashboard after registration."
        # Assert: Expected the registration form submit button to be hidden after successful registration.
        await expect(page.locator("xpath=/html/body/main/div/div/div/div/form/button").nth(0)).not_to_be_visible(timeout=15000), "Expected the registration form submit button to be hidden after successful registration."
        await asyncio.sleep(5)

    finally:
        if context:
            await context.close()
        if browser:
            await browser.close()
        if pw:
            await pw.stop()

asyncio.run(run_test())
    