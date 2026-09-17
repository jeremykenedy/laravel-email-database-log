const { test, expect } = require("@playwright/test");
const AxeBuilder = require("@axe-core/playwright").default;

for (const framework of ["standalone", "bootstrap5", "tailwind"]) {
  for (const theme of ["light", "dark"]) {
    test(`${framework} ${theme}: browse, search, read safely, and keep theme`, async ({
      page,
    }) => {
      const errors = [];
      page.on("pageerror", (error) => errors.push(error.message));
      await page.emulateMedia({ colorScheme: theme });
      await page.goto(`/email-log?framework=${framework}`);
      await expect(
        page.getByRole("heading", { name: "Outgoing emails" }),
      ).toBeVisible();
      await expect(page.locator("tbody tr")).toHaveCount(5);
      await page.getByLabel("Appearance").selectOption(theme);
      await expect(page.locator("html")).toHaveAttribute(
        "data-bs-theme",
        theme,
      );
      const audit = await new AxeBuilder({ page })
        .withTags(["wcag2a", "wcag2aa", "wcag21aa"])
        .analyze();
      expect(audit.violations).toEqual([]);
      await page.getByRole("link", { name: "Next", exact: true }).click();
      await expect(page.locator("tbody tr")).toHaveCount(2);
      await page.getByRole("link", { name: "Previous", exact: true }).click();
      await page.getByLabel("Search email history").fill("Payment");
      await page.getByRole("button", { name: "Search", exact: true }).click();
      await expect(page.locator("tbody tr")).toHaveCount(1);
      await page
        .getByRole("link", { name: "Payment received", exact: true })
        .click();
      await expect(
        page.getByRole("heading", { name: "Message source", exact: true }),
      ).toBeVisible();
      await expect(page.locator(".el-source").first()).toContainText(
        "<script>",
      );
      expect(await page.evaluate(() => window.emailExecuted)).toBeUndefined();
      await page.reload();
      await expect(page.getByLabel("Appearance")).toHaveValue(theme);
      const detailsAudit = await new AxeBuilder({ page })
        .withTags(["wcag2a", "wcag2aa", "wcag21aa"])
        .analyze();
      expect(detailsAudit.violations).toEqual([]);
      expect(errors).toEqual([]);
    });
  }
  test(`${framework}: mobile layout and keyboard access`, async ({ page }) => {
    await page.setViewportSize({ width: 390, height: 844 });
    await page.goto(`/email-log?framework=${framework}`);
    expect(
      await page.evaluate(
        () => document.documentElement.scrollWidth <= window.innerWidth,
      ),
    ).toBe(true);
    await page.keyboard.press("Tab");
    await expect(
      page.getByRole("link", { name: "Skip to content" }),
    ).toBeFocused();
    await page.keyboard.press("Enter");
    await expect(page.locator("main")).toBeFocused();
    await page.getByLabel("Search email history").fill("No matching message");
    await page.getByRole("button", { name: "Search", exact: true }).click();
    await expect(
      page.getByRole("heading", { name: "No matching emails" }),
    ).toBeVisible();
  });
}

test("system theme follows OS and works with unavailable local storage", async ({
  page,
}) => {
  await page.addInitScript(() => {
    Object.defineProperty(window, "localStorage", {
      get() {
        throw new Error("Storage disabled");
      },
    });
  });
  await page.emulateMedia({ colorScheme: "dark" });
  await page.goto("/email-log");
  await expect(page.locator("html")).toHaveAttribute("data-bs-theme", "dark");
  await page.emulateMedia({ colorScheme: "light" });
  await expect(page.locator("html")).toHaveAttribute("data-bs-theme", "light");
  await page.getByLabel("Appearance").selectOption("dark");
  await expect(page.locator("html")).toHaveAttribute("data-bs-theme", "dark");
});

test("README screenshots", async ({ page }) => {
  test.skip(
    !process.env.UPDATE_ART,
    "Set UPDATE_ART=1 to refresh the README screenshots.",
  );
  await page.setViewportSize({ width: 1280, height: 860 });
  for (const theme of ["light", "dark"]) {
    await page.goto("/email-log");
    await page.getByLabel("Appearance").selectOption(theme);
    await page.screenshot({
      path: `art/dashboard-${theme}.png`,
      fullPage: true,
    });
  }
});
